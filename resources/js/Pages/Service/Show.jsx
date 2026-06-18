import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';

import NavbarS2      from '@react/components/NavbarS2/NavbarS2';
import PageTitle     from '@react/components/pagetitle/PageTitle';
import Accordion     from '@react/components/Accordion/Accordion';
import ServiceSidebar from '@react/main-component/ServiceSinglePage/sidebar';
import CtaSectionS2  from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer        from '@react/components/footer/Footer';

import Video  from '@react/img/service/details-2.jpg';
import simg1  from '@react/img/service/details-3.jpg';
import simg2  from '@react/img/service/details-4.jpg';

const ServiceShow = ({ service = null }) => {
    const title = service ? service.title : 'Service Details';
    const description = service ? service.description : '';
    const sImg = service ? service.sImg : simg1;

    return (
        <Fragment>
            <Head title={title} />
            <NavbarS2 hclass="header-section-2 style-two" />
            <PageTitle pageTitle={title} pagesub="Service Details" />
            <section className="service-details-section fix section-padding section-bg-2">
                <div className="container">
                    <div className="service-details-wrapper">
                        <div className="row g-5">
                            <div className="col-lg-4 order-2 order-md-1">
                                <ServiceSidebar />
                            </div>
                            <div className="col-lg-8 order-1 order-md-2">
                                <div className="service-details-image">
                                    <img src={sImg} alt={title} />
                                </div>
                                <div className="service-details-content">
                                    <h3>{title}</h3>
                                    {description && <p className="mt-3">{description}</p>}
                                    <div className="row g-4 mt-3">
                                        <div className="col-md-6">
                                            <img src={simg1} alt="" className="img-fluid rounded" />
                                        </div>
                                        <div className="col-md-6">
                                            <img src={simg2} alt="" className="img-fluid rounded" />
                                        </div>
                                    </div>
                                    <Accordion />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <CtaSectionS2 />
            <Footer />
        </Fragment>
    );
};

export default ServiceShow;
