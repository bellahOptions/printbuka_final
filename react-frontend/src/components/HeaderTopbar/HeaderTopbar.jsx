import React from 'react'
import { Link } from 'react-router-dom'
import CurrentDoler from './CurrentDoler';

const HeaderTopbar = (props) => {
    const ClickHandler = () => {
        window.scrollTo(10, 0);
    }

    return (
        <div className="container-fluid">
            <div className="header-top-wrapper">
                <p><a href="tel:+2348100000000">+234 (810) 000 0000</a> &nbsp;(Mon &ndash; Sat 8am to 6pm)</p>
                <p>Nigeria&apos;s #1 Online Print Shop &mdash; Delivered to all 36 states</p>
                <div className="header-top-right">
                    <div className="social-icon d-flex align-items-center">
                        <Link onClick={ClickHandler} to="#"><i className="fab fa-facebook-f"></i></Link>
                        <Link onClick={ClickHandler} to="#"><i className="fab fa-twitter"></i></Link>
                        <Link onClick={ClickHandler} to="#"><i className="fab fa-instagram"></i></Link>
                        <Link onClick={ClickHandler} to="#"><i className="fab fa-linkedin-in"></i></Link>
                    </div>
                    <CurrentDoler />
                </div>
            </div>
        </div>
    )
}

export default HeaderTopbar;
