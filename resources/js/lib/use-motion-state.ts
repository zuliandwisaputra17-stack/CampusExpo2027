import { useEffect, useState, type RefObject } from 'react';

/** Suspend animation in background tabs, outside the viewport, or on request. */
export function useMotionState(ref: RefObject<HTMLElement | null>) {
    const [reducedMotion, setReducedMotion] = useState(() =>
        typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    );
    const [visible, setVisible] = useState(false);
    const [documentVisible, setDocumentVisible] = useState(() =>
        typeof document === 'undefined' || !document.hidden,
    );

    useEffect(() => {
        const media = window.matchMedia('(prefers-reduced-motion: reduce)');
        const change = () => setReducedMotion(media.matches);
        media.addEventListener('change', change);
        const visibility = () => setDocumentVisible(!document.hidden);
        document.addEventListener('visibilitychange', visibility);
        const observer = new IntersectionObserver(([entry]) => setVisible(entry.isIntersecting));
        if (ref.current) observer.observe(ref.current);
        return () => {
            observer.disconnect();
            media.removeEventListener('change', change);
            document.removeEventListener('visibilitychange', visibility);
        };
    }, [ref]);

    return { reducedMotion, visible: visible && documentVisible };
}
