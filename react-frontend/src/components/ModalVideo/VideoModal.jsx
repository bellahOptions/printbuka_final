import React, { useState } from 'react';

const VideoModal = () => {
    const [isOpen, setOpen] = useState(false);

    return (
        <React.Fragment>
            {isOpen && (
                <div className="modal-overlay" onClick={() => setOpen(false)} style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,.85)', zIndex: 9999, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <iframe
                        width="800" height="450"
                        src="https://www.youtube.com/embed/pNje3bWz7V8?autoplay=1"
                        title="Printbuka"
                        frameBorder="0"
                        allow="autoplay; encrypted-media"
                        allowFullScreen
                    />
                </div>
            )}
            <div className="video-btn">
                <button className="video-btn ripple video-popup" onClick={() => setOpen(true)}>
                    <i className="fas fa-play"></i>
                </button>
            </div>
        </React.Fragment>
    );
};

export default VideoModal;
