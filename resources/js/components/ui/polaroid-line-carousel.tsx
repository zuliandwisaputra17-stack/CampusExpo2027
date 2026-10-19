import * as React from 'react';
import { ArrowLeft, ArrowRight, ArrowUpRight, Camera, BriefcaseBusiness, Pause, Play } from 'lucide-react';
import { useMotionState } from '../../lib/use-motion-state';
import './polaroid-line-carousel.css';

export type MemberSlide = {
    id?: number | string;
    title?: string;
    name?: string;
    caption?: string;
    image?: string | null;
    alt?: string;
    campus?: string;
    officialUrl?: string | null;
    faculty?: string;
    program?: string;
    cohort?: number | string;
    instagramUrl?: string | null;
    linkedinUrl?: string | null;
};

export type Slide = MemberSlide;

export type PolaroidLineCarouselProps = {
    slides?: MemberSlide[];
    cardWidth?: number;
    sag?: number;
    swing?: number;
    autoplay?: number;
    string?: string;
    onChange?: (index: number) => void;
    className?: string;
    style?: React.CSSProperties;
    ariaLabel?: string;
};

const clamp = (value: number, min: number, max: number) => Math.min(max, Math.max(min, value));
const wrap = (value: number, total: number) => ((value % total) + total) % total;
const nearestAt = (off: number, spacing: number, count: number) => clamp(Math.round(off / spacing), 0, Math.max(0, count - 1));
const stringY = (x: number, width: number, y: number, sag: number) => {
    const t = clamp(x / width, 0, 1);
    return y + 4 * sag * t * (1 - t);
};
const nameOf = (slide: MemberSlide) => slide.name || slide.title || 'Anggota Campus Expo';
const pad = (value: number) => String(value).padStart(2, '0');

function safeLink(value: string | null | undefined) {
    if (!value) return undefined;
    try {
        const url = new URL(value);
        return ['http:', 'https:'].includes(url.protocol) ? url.href : undefined;
    } catch { return undefined; }
}

type Drag = { id: number; x: number; y: number; off: number; lx: number; lt: number; v: number; moved: boolean };

/** Spring-driven prints pegged to a curved string, adapted from the supplied reference. */
export function PolaroidLineCarousel({
    slides = [], cardWidth = 248, sag = 34, swing = .8, autoplay = 6000,
    string = '#7cadd2', onChange, className, style, ariaLabel = 'Kenalan dengan anggota Campus Expo',
}: PolaroidLineCarouselProps) {
    const rootRef = React.useRef<HTMLDivElement>(null);
    const stageRef = React.useRef<HTMLDivElement>(null);
    const pathRef = React.useRef<SVGPathElement>(null);
    const cards = React.useRef<Array<HTMLButtonElement | null>>([]);
    const motion = useMotionState(rootRef);
    const [active, setActive] = React.useState(0);
    const [dragging, setDragging] = React.useState(false);
    const [paused, setPaused] = React.useState(false);
    const [hovered, setHovered] = React.useState(false);
    const [focused, setFocused] = React.useState(false);
    const [tick, setTick] = React.useState(0);
    const [size, setSize] = React.useState({ w: 1200, cw: cardWidth });
    const sim = React.useRef({ off: 0, vel: 0, target: 0, angles: [] as number[], angularVelocity: [] as number[] });
    const drag = React.useRef<Drag | null>(null);
    const suppressClick = React.useRef(false);
    const activeRef = React.useRef(0);
    const lastTouch = React.useRef(0);
    const count = slides.length;
    const spacing = size.cw * 1.13;
    // A long line wraps beyond the viewport, keeping portraits on both sides.
    // Short filtered lists keep natural endpoints without duplicate people.
    const loops = count * spacing > size.w + size.cw * 2;
    const stopped = paused || motion.reducedMotion;
    // Stable IDs detect a filter change even when two filters return equal counts.
    const slideKey = JSON.stringify(slides.map(slide => [slide.id, slide.name, slide.title, slide.image]));

    const goTo = React.useCallback((index: number) => {
        if (loops) {
            const current = sim.current.off / spacing;
            const nearest = current + wrap(index - current + count / 2, count) - count / 2;
            sim.current.target = nearest * spacing;
        } else {
            sim.current.target = clamp(index, 0, Math.max(0, count - 1)) * spacing;
        }
        setTick(value => value + 1);
    }, [count, spacing, loops]);

    React.useEffect(() => {
        const stage = stageRef.current;
        if (!stage) return;
        const observer = new ResizeObserver(([entry]) => {
            const width = entry.contentRect.width;
            const cw = Math.round(Math.min(cardWidth, Math.max(150, width * .62)));
            setSize(previous => previous.w === width && previous.cw === cw ? previous : { w: width, cw });
        });
        observer.observe(stage);
        return () => observer.disconnect();
    }, [cardWidth, count]);

    React.useEffect(() => {
        activeRef.current = 0;
        setActive(0);
        setDragging(false);
        drag.current = null;
        sim.current = { off: 0, vel: 0, target: 0, angles: [], angularVelocity: [] };
    }, [slideKey]);

    React.useEffect(() => {
        sim.current.target = activeRef.current * spacing;
        sim.current.off = sim.current.target;
        sim.current.vel = 0;
    }, [spacing]);

    React.useEffect(() => { if (count) onChange?.(active); }, [active, onChange, count]);

    React.useEffect(() => {
        if (!count) return;
        let raf = 0;
        let previous = performance.now();
        const frame = (now: number) => {
            const dt = Math.min(.033, Math.max(.001, (now - previous) / 1000));
            previous = now;
            const state = sim.current;
            const isDragging = drag.current?.moved;
            let lineVelocity = 0;
            if (isDragging) {
                lineVelocity = (drag.current?.v ?? 0) * 1000;
            } else if (stopped) {
                state.off = state.target;
                state.vel = 0;
            } else {
                const before = state.off;
                state.vel += (70 * (state.target - state.off) - 2 * Math.sqrt(70) * state.vel) * dt;
                state.off += state.vel * dt;
                lineVelocity = -(state.off - before) / dt;
            }
            const near = loops ? wrap(Math.round(state.off / spacing), count) : nearestAt(state.off, spacing, count);
            const y0 = 30;
            for (let i = 0; i < count; i++) {
                const card = cards.current[i];
                if (!card) continue;
                const distance = i * spacing - state.off;
                const totalWidth = count * spacing;
                const x = size.w / 2 + (loops ? wrap(distance + totalWidth / 2, totalWidth) - totalWidth / 2 : distance);
                const offscreen = x < -size.cw || x > size.w + size.cw;
                card.style.visibility = offscreen ? 'hidden' : 'visible';
                if (offscreen) continue;
                let angle = state.angles[i] || 0;
                let velocity = state.angularVelocity[i] || 0;
                if (motion.reducedMotion) { angle = 0; velocity = 0; }
                else if (!stopped) {
                    velocity += (-38 * angle - 4.2 * velocity + lineVelocity * .0034 * swing) * dt;
                    angle = clamp(angle + velocity * dt, -.42, .42);
                    angle += Math.sin(now / 1300 + i * 1.7) * .0009 * swing;
                }
                state.angles[i] = angle;
                state.angularVelocity[i] = velocity;
                const y = stringY(x, size.w, y0, sag) - 6;
                card.style.transform = `translate(${(x - size.cw / 2).toFixed(1)}px,${y.toFixed(1)}px) rotate(${angle.toFixed(4)}rad)`;
                card.style.zIndex = String(i === near ? count + 1 : count - Math.abs(i - near));
            }
            pathRef.current?.setAttribute('d', `M0 ${y0} Q${size.w / 2} ${y0 + 2 * sag} ${size.w} ${y0}`);
            if (near !== activeRef.current) { activeRef.current = near; setActive(near); }
            if (motion.visible && !stopped) raf = requestAnimationFrame(frame);
        };
        // One layout frame is also needed for static/reduced-motion presentations.
        raf = requestAnimationFrame(frame);
        return () => cancelAnimationFrame(raf);
    }, [size, spacing, count, slideKey, loops, motion.visible, motion.reducedMotion, stopped, swing, sag, tick]);

    React.useEffect(() => {
        if (!autoplay || count < 2 || stopped || !motion.visible || hovered || focused || dragging) return;
        const timer = window.setInterval(() => {
            if (!drag.current && performance.now() - lastTouch.current >= autoplay) {
                goTo(activeRef.current >= count - 1 ? 0 : activeRef.current + 1);
            }
        }, autoplay);
        return () => window.clearInterval(timer);
    }, [autoplay, count, stopped, motion.visible, hovered, focused, dragging, goTo]);

    const step = (direction: number) => {
        lastTouch.current = performance.now();
        goTo(activeRef.current + direction);
    };
    const onKey = (event: React.KeyboardEvent) => {
        if (event.key === 'ArrowRight') step(1);
        else if (event.key === 'ArrowLeft') step(-1);
        else if (event.key === 'Home') goTo(0);
        else if (event.key === 'End') goTo(count - 1);
        else return;
        event.preventDefault();
        // Keep keyboard focus on the stationary stage while the chosen print moves.
        if ((event.target as HTMLElement).closest('.expo-polaroids__card')) stageRef.current?.focus({ preventScroll: true });
    };
    const onDown = (event: React.PointerEvent<HTMLDivElement>) => {
        if (!event.isPrimary || event.button !== 0 || count < 2) return;
        suppressClick.current = false;
        lastTouch.current = performance.now();
        drag.current = { id: event.pointerId, x: event.clientX, y: event.clientY, off: sim.current.off, lx: event.clientX, lt: event.timeStamp, v: 0, moved: false };
    };
    const onMove = (event: React.PointerEvent<HTMLDivElement>) => {
        const current = drag.current;
        if (!current || current.id !== event.pointerId) return;
        const dx = event.clientX - current.x;
        const dy = event.clientY - current.y;
        if (!current.moved && Math.abs(dy) > 10 && Math.abs(dy) > Math.abs(dx)) { drag.current = null; return; }
        if (!current.moved && Math.abs(dx) > 6) {
            current.moved = true;
            suppressClick.current = true;
            setDragging(true);
            stageRef.current?.setPointerCapture(event.pointerId);
        }
        if (!current.moved) return;
        const dt = Math.max(1, event.timeStamp - current.lt);
        current.v = .7 * (event.clientX - current.lx) / dt + .3 * current.v;
        current.lx = event.clientX;
        current.lt = event.timeStamp;
        const max = (count - 1) * spacing;
        let off = current.off - dx;
        if (!loops && off < 0) off *= .3;
        if (!loops && off > max) off = max + (off - max) * .3;
        sim.current.off = off;
        if (stopped) setTick(value => value + 1);
    };
    const finishDrag = (event: React.PointerEvent<HTMLDivElement>) => {
        const current = drag.current;
        if (!current || current.id !== event.pointerId) return;
        drag.current = null;
        setDragging(false);
        lastTouch.current = performance.now();
        if (stageRef.current?.hasPointerCapture(event.pointerId)) stageRef.current.releasePointerCapture(event.pointerId);
        if (!current.moved) return;
        const canceled = event.type !== 'pointerup';
        sim.current.vel = canceled || stopped ? 0 : -current.v * 1000;
        const projected = sim.current.off - (canceled || stopped ? 0 : current.v * 180);
        goTo(loops ? wrap(Math.round(projected / spacing), count) : nearestAt(projected, spacing, count));
    };

    const slide = slides[Math.min(active, Math.max(0, count - 1))];
    const instagram = safeLink(slide?.instagramUrl);
    const linkedin = safeLink(slide?.linkedinUrl);
    const campusLink = safeLink(slide?.officialUrl);
    const live = stopped || focused || hovered || !autoplay ? 'polite' : 'off';

    return <div ref={rootRef} className={['expo-polaroids', className].filter(Boolean).join(' ')}
        style={{ '--ep-card-width': `${size.cw}px`, ...style } as React.CSSProperties}
        role="region" aria-roledescription="karusel" aria-label={ariaLabel}
        data-reduced-motion={motion.reducedMotion ? 'true' : 'false'} data-paused={stopped ? 'true' : 'false'}
        onMouseEnter={() => setHovered(true)} onMouseLeave={() => setHovered(false)}
        onFocusCapture={() => setFocused(true)} onBlurCapture={event => {
            if (!event.currentTarget.contains(event.relatedTarget as Node | null)) setFocused(false);
        }} onKeyDown={onKey}>
        {!slide ? <div className="expo-polaroids__empty"><span>Belum ada anggota di pilihan ini.</span><p>Coba kampus lain untuk berkenalan dengan kakak alumni.</p></div> : <>
            <div ref={stageRef} className="expo-polaroids__stage" data-dragging={dragging ? 'true' : 'false'}
                style={{ height: `${size.cw + 158}px` }} tabIndex={0}
                aria-label="Geser foto atau gunakan tombol panah kiri dan kanan untuk memilih anggota."
                onPointerDown={onDown} onPointerMove={onMove} onPointerUp={finishDrag} onPointerCancel={finishDrag} onLostPointerCapture={finishDrag}>
                <svg className="expo-polaroids__string" aria-hidden="true"><path ref={pathRef} fill="none" stroke={string} strokeWidth="1.7" strokeLinecap="round" /></svg>
                {slides.map((member, index) => <button type="button" key={member.id ?? `${index}-${nameOf(member)}`}
                    className="expo-polaroids__card" data-active={index === active ? 'true' : 'false'}
                    ref={element => { cards.current[index] = element; }} tabIndex={index === active ? 0 : -1}
                    aria-label={`${nameOf(member)}${member.campus ? `, ${member.campus}` : ''}. Lihat profil.`}
                    aria-pressed={index === active} onClick={() => {
                        if (suppressClick.current) { suppressClick.current = false; return; }
                        lastTouch.current = performance.now(); goTo(index);
                    }}>
                    <span className="expo-polaroids__print">
                        <span className="expo-polaroids__shot">
                            <span className="expo-polaroids__initials" aria-hidden="true">{nameOf(member).split(/\s+/).slice(0, 2).map(word => word[0]).join('')}</span>
                            {member.image ? <img key={member.image} src={member.image} alt={member.alt || nameOf(member)} loading="lazy" decoding="async" draggable={false} onError={event => { event.currentTarget.style.display = 'none'; }} /> : null}
                            <span className="expo-polaroids__number" aria-hidden="true">{pad(index + 1)}</span>
                        </span>
                        <span className="expo-polaroids__note">{nameOf(member)}</span>
                        <span className="expo-polaroids__campus">{member.campus || 'Keluarga Campus Expo'}</span>
                    </span>
                    <span className="expo-polaroids__peg" aria-hidden="true" />
                </button>)}
            </div>
            <div className="expo-polaroids__bottom">
                <div className="expo-polaroids__profile">
                    <span className="expo-polaroids__eyebrow">KENALAN DENGAN KAKAK ALUMNI</span>
                    <h3>{nameOf(slide)}</h3>
                    <p className="expo-polaroids__study">{[slide.program, slide.faculty].filter(Boolean).join(' · ') || slide.caption || 'Siap berbagi cerita perjalanan kuliah.'}</p>
                    <p className="expo-polaroids__meta">{slide.campus}{slide.cohort ? ` · Angkatan ${slide.cohort}` : ''}</p>
                    <div className="expo-polaroids__socials">
                        {instagram ? <a href={instagram} target="_blank" rel="noopener noreferrer" aria-label={`Instagram ${nameOf(slide)} (tab baru)`}><Camera size={15} aria-hidden="true" />Instagram<ArrowUpRight size={12} aria-hidden="true" /></a> : null}
                        {linkedin ? <a href={linkedin} target="_blank" rel="noopener noreferrer" aria-label={`LinkedIn ${nameOf(slide)} (tab baru)`}><BriefcaseBusiness size={15} aria-hidden="true" />LinkedIn<ArrowUpRight size={12} aria-hidden="true" /></a> : null}
                        {campusLink ? <a className="expo-polaroids__campus-link" href={campusLink} target="_blank" rel="noopener noreferrer" aria-label={`Situs resmi ${slide.campus || 'kampus'} (tab baru)`}>Situs kampus<ArrowUpRight size={13} aria-hidden="true" /></a> : null}
                    </div>
                </div>
                <div className="expo-polaroids__navigation">
                    <span className="expo-polaroids__count" aria-hidden="true"><b>{pad(active + 1)}</b><span> / {pad(count)}</span></span>
                    <div className="expo-polaroids__buttons">
                        <button type="button" className="expo-polaroids__button" aria-label="Anggota sebelumnya" disabled={count < 2 || (!loops && active === 0)} onClick={() => step(-1)}><ArrowLeft size={19} aria-hidden="true" /></button>
                        {count > 1 && autoplay > 0 && !motion.reducedMotion ? <button type="button" className="expo-polaroids__button expo-polaroids__pause" aria-label={paused ? 'Putar animasi anggota' : 'Jeda animasi anggota'} aria-pressed={paused} onClick={() => setPaused(value => !value)}>{paused ? <Play size={16} aria-hidden="true" /> : <Pause size={16} aria-hidden="true" />}</button> : null}
                        <button type="button" className="expo-polaroids__button" aria-label="Anggota berikutnya" disabled={count < 2 || (!loops && active === count - 1)} onClick={() => step(1)}><ArrowRight size={19} aria-hidden="true" /></button>
                    </div>
                    <span className="expo-polaroids__hint">Geser & temukan ceritanya</span>
                </div>
            </div>
            <span className="expo-polaroids__sr" aria-live={live} aria-atomic="true">Anggota {active + 1} dari {count}: {nameOf(slide)}{slide.campus ? `, ${slide.campus}` : ''}.</span>
        </>}
    </div>;
}

export default PolaroidLineCarousel;
