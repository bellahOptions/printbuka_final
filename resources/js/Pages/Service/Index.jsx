import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';

import NavbarS2       from '@react/components/NavbarS2/NavbarS2';
import PageTitle      from '@react/components/pagetitle/PageTitle';
import ServiceSection from '@react/components/ServiceSection/ServiceSection';
import FunFact        from '@react/components/FunFact/FunFact';
import ProcessSection from '@react/components/ProcessSection/ProcessSection';
import CtaSectionS2   from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer         from '@react/components/footer/Footer';

import ServiceBg from '@react/img/service/service-bg.jpg';

const ServiceIndex = () => (
    <Fragment>
        <Head title="Our Services" />
        <NavbarS2 hclass="header-section-2 style-two" />
        <PageTitle pageTitle="Our Printing Services" pagesub="Services" />
        <ServiceSection hclass="service-section bg-cover section-padding" Bg={ServiceBg} />
        <FunFact hclass="counter-section fix section-padding" />
        <ProcessSection />
        <CtaSectionS2 />
        <Footer />
    </Fragment>
);

export default ServiceIndex;
