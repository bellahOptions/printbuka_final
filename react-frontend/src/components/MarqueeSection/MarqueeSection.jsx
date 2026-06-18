import React from 'react';

import MS1 from '../../img/marquee-box.png'

const MarqueeSection = (props) => {
    return (
        <div className={"" +props.hclass}>
            <div className="mycustom-marque">
                <div className="scrolling-wrap">
                    <div className="comm">
                        <div className="cmn-textslide">Business Cards</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide">Sticker Printing</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide color-2">UV-DTF Transfers</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide">Branded Gifts</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide">Laser Engraving</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide color-2">DTF Printing</div>
                        <div><img src={MS1} alt="img" /></div>
                    </div>
                    <div className="comm">
                        <div className="cmn-textslide">Business Cards</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide">Sticker Printing</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide color-2">UV-DTF Transfers</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide">Branded Gifts</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide">Laser Engraving</div>
                        <div><img src={MS1} alt="img" /></div>
                        <div className="cmn-textslide color-2">DTF Printing</div>
                        <div><img src={MS1} alt="img" /></div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MarqueeSection;