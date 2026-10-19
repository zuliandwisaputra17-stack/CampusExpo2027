import { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { Pause, Play } from 'lucide-react';
import { TiltedGridHero, type TiltedGridImage } from '@/components/ui/tilted-grid-hero';
import PolaroidLineCarousel, { type Slide } from '@/components/ui/polaroid-line-carousel';

function readData<T>(id: string): T[] {
    const element = document.getElementById(id);
    if (!element) return [];
    try {
        const data: unknown = JSON.parse(element.textContent || '[]');
        return Array.isArray(data) ? data : [];
    } catch {
        return [];
    }
}

function CampusReel({ images }: { images: TiltedGridImage[] }) {
    const [paused, setPaused] = useState(false);
    useEffect(() => {
        document.getElementById('campus')?.classList.add('has-reel');
    }, []);
    return <div className="campus-reel" aria-label="Panorama kampus">
        <TiltedGridHero images={images} speed={5} tileHeight={66} aspectRatio={1.48}
            curve={65} fade={8} axis={47} paused={paused} className="campus-curved-band" />
        <div className="reel-toolbar">
            <span><span className="tiny-dot" /> A WORLD OF POSSIBILITIES</span>
            <button type="button" className="reel-pause" onClick={() => setPaused(value => !value)}
                aria-label={paused ? 'Putar animasi kampus' : 'Jeda animasi kampus'} aria-pressed={paused}>
                {paused ? <Play size={14} /> : <Pause size={14} />}
                {paused ? 'Putar' : 'Jeda'}
            </button>
        </div>
    </div>;
}

function MemberCarousel({ slides }: { slides: Slide[] }) {
    useEffect(() => {
        // Keep the complete server-rendered directory available if JS fails to load.
        const fallback = document.getElementById('member-directory');
        if (fallback) fallback.hidden = true;
    }, []);
    return <PolaroidLineCarousel slides={slides} autoplay={6500} cardWidth={260}
        ariaLabel="Kenalan dengan mahasiswa dan alumni" />;
}

export function mountShowcases() {
    const campusElement = document.getElementById('campus-reel-root');
    const images = readData<TiltedGridImage>('campus-reel-data');
    if (campusElement && images.length) {
        createRoot(campusElement).render(<CampusReel images={images} />);
    }

    const memberElement = document.getElementById('member-carousel-root');
    if (!memberElement) return;
    const slides = readData<Slide>('member-carousel-data');
    const root = createRoot(memberElement);
    const renderMembers = () => {
        const ids = new Set(Array.from(document.querySelectorAll<HTMLElement>('[data-member]:not([hidden])'))
            .map(card => card.dataset.memberId));
        const filtered = slides.filter(slide => ids.has(String(slide.id)));
        memberElement.hidden = filtered.length === 0;
        memberElement.dataset.count = String(filtered.length);
        root.render(<MemberCarousel slides={filtered} />);
    };
    document.addEventListener('expo:members-filtered', renderMembers);
    renderMembers();
}
