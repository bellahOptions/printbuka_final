import React from 'react'
import CountUp from 'react-countup';
const FunFact = (props) => {

    return (
        <section className={"" + props.hclass}>
            <div className="container">
                <div className="counter-text text-center">
                    <h6 className="wow fadeInUp">Trusted by businesses and individuals across Nigeria</h6>
                </div>
                <div className="row">
                    <div className="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                        <div className="counter-items">
                            <div className="counter-title">
                                <h2><span><CountUp end={15} enableScrollSpy /></span>k+</h2>
                            </div>
                            <p className="text-center">Orders Completed</p>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                        <div className="counter-items">
                            <div className="counter-title bg-2">
                                <h2><span><CountUp end={36} enableScrollSpy /></span></h2>
                            </div>
                            <p className="text-center">States Served</p>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                        <div className="counter-items">
                            <div className="counter-title bg-3">
                                <h2><span><CountUp end={24} enableScrollSpy /></span>h</h2>
                            </div>
                            <p className="text-center">File Review Turnaround</p>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".8s">
                        <div className="counter-items">
                            <div className="counter-title bg-4">
                                <h2><span><CountUp end={98} enableScrollSpy /></span>%</h2>
                            </div>
                            <p className="text-center">Satisfaction Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    )
}

export default FunFact;