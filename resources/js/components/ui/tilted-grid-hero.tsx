import * as React from 'react';
import { useMotionState } from '../../lib/use-motion-state';
import './tilted-grid-hero.css';

const SLICES = 16;
const CAMERA = 1.6;
const MAX_WIDTH = 55;

function arc(bend: number) {
    return (CAMERA - 1 + Math.cos(bend)) / (2 * CAMERA * Math.sin(bend));
}

function measure(width: number, height: number, tile: number, aspect: number, gap: number, bend: number) {
    const h = Math.min((tile / 100) * height, ((MAX_WIDTH / 100) * width) / aspect);
    const radius = width * arc(bend);
    if (!(h > 0) || !(radius > 0)) return { columns: 2, sweep: 0, unit: 0, limit: 0 };
    const deg = (rad: number) => rad * 180 / Math.PI;
    const unit = deg(h / radius);
    const pitch = (aspect + gap / 100) * unit;
    const limit = deg(bend) + aspect * unit / 2;
    const columns = Math.min(60, Math.max(2, Math.ceil(2 * limit / pitch)));
    const round = (n: number) => +n.toFixed(4);
    const sweep = round(columns * pitch / 2);
    return { columns, sweep, unit: round(unit), limit: round(Math.min(sweep, limit)) };
}

export type TiltedGridImage = { src: string; alt?: string };
export type TiltedGridHeroProps = React.ComponentProps<'div'> & {
    images: TiltedGridImage[];
    paused?: boolean;
    speed?: number;
    tileHeight?: number;
    aspectRatio?: number;
    gap?: number;
    axis?: number;
    curve?: number;
    fade?: number;
};

/** The supplied cylinder effect: each image is bent using sixteen 3D facets. */
export function TiltedGridHero({
    images, paused = false, speed = 5, tileHeight = 64, aspectRatio = 1.5,
    gap = 8, axis = 50, curve = 70, fade = 12, children, className, style, ...props
}: TiltedGridHeroProps) {
    const ref = React.useRef<HTMLDivElement>(null);
    const motion = useMotionState(ref);
    const id = React.useId().replace(/[^a-zA-Z0-9]/g, '');
    const orbit = `expo-orbit-${id}`;
    const bend = Math.min(85, Math.max(5, curve)) * Math.PI / 180;
    const aspect = Math.max(.25, aspectRatio);
    const height = Math.max(1, tileHeight);
    const tileSpeed = Math.max(.5, speed);
    const [layout, setLayout] = React.useState<ReturnType<typeof measure> | null>(null);
    const [shown, setShown] = React.useState<Record<number, number>>({});

    React.useEffect(() => {
        if (!ref.current) return;
        const ro = new ResizeObserver(([entry]) => {
            const next = measure(entry.contentRect.width, entry.contentRect.height, height, aspect, Math.max(0, gap), bend);
            setLayout(prev => prev?.columns === next.columns && prev.sweep === next.sweep && prev.unit === next.unit ? prev : next);
        });
        ro.observe(ref.current);
        return () => ro.disconnect();
    }, [height, aspect, gap, bend]);

    // Preload only once the reel approaches view; image switches occur offscreen.
    React.useEffect(() => {
        if (motion.visible) images.forEach(({ src }) => { const image = new Image(); image.src = src; });
    }, [images, motion.visible]);

    const { columns, sweep, unit, limit } = layout ?? measure(1200, 410, height, aspect, gap, bend);
    const u = (n: number) => `calc(${+n.toFixed(4)} * min(${height}cqh, ${+(MAX_WIDTH / aspect).toFixed(4)}cqw))`;
    const r = 100 * arc(bend);
    const radius = `${+r.toFixed(3)}cqw`;
    const turn = (deg: number) => `translateZ(${radius}) rotateY(${+deg.toFixed(4)}deg) translateZ(-${radius})`;
    const hide = +(((sweep - limit) / (2 * sweep || 1)) * 100).toFixed(4);
    const css = `@keyframes ${orbit}{from{transform:${turn(-sweep)}}to{transform:${turn(sweep)}}0%,${hide}%,${100 - hide}%,100%{visibility:hidden}${hide + .001}%,${100 - hide - .001}%{visibility:visible}}`;
    const share = aspect / SLICES;
    const duration = columns * tileSpeed;
    const fadeSize = Math.min(40, Math.max(0, fade));
    const mask = `linear-gradient(90deg,transparent,#000 ${fadeSize}%,#000 ${100 - fadeSize}%,transparent)`;
    const stopped = paused || motion.reducedMotion || !motion.visible || images.length < 2;

    return (
        <div ref={ref} className={['expo-reel', className].filter(Boolean).join(' ')} style={style} {...props}>
            <style>{css}</style>
            {images.length ? <div aria-hidden="true" className="expo-reel__band" style={{
                opacity: layout ? 1 : 0, perspective: `${+(r * CAMERA).toFixed(3)}cqw`,
                perspectiveOrigin: `50% ${axis}%`, maskImage: mask, WebkitMaskImage: mask,
            }}>
                {Array.from({ length: columns }, (_, t) => {
                    const first = columns - 1 - t;
                    const img = images[(shown[t] ?? first) % images.length];
                    return <div key={t} className="expo-reel__tile" style={{
                        left: `calc(50% - ${u(aspect / 2)})`, top: `calc(${axis}% - ${u(.5)})`,
                        width: u(aspect), height: u(1), animation: `${orbit} ${duration}s linear ${-t * tileSpeed}s infinite`,
                        animationPlayState: stopped ? 'paused' : 'running',
                    }} onAnimationIteration={event => {
                        const next = Math.round(event.elapsedTime / duration) * columns + first;
                        setShown(prev => prev[t] === next ? prev : { ...prev, [t]: next });
                    }}>
                        {Array.from({ length: SLICES }, (_, k) => <div key={k} className="expo-reel__slice" style={{
                            left: u((aspect - share) / 2), width: k === SLICES - 1 ? u(share) : `calc(${u(share)} + 1px)`,
                            height: u(1), transform: turn((aspect / 2 - (k + .5) * share) * unit),
                        }}>
                            <img src={img.src} alt="" draggable={false} decoding="async" style={{ left: u(-k * share), width: u(aspect), height: u(1) }} />
                        </div>)}
                    </div>;
                })}
            </div> : <p className="expo-reel__empty">Kampus peserta akan segera hadir.</p>}
            {children}
        </div>
    );
}

export default TiltedGridHero;
