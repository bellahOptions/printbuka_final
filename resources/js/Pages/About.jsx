import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';

import NavbarS2            from '@react/components/NavbarS2/NavbarS2';
import PageTitle           from '@react/components/pagetitle/PageTitle';
import About2              from '@react/components/about2/about2';
import MarqueeSection      from '@react/components/MarqueeSection/MarqueeSection';
import ServiceSection      from '@react/components/ServiceSection/ServiceSection';
import FunFact             from '@react/components/FunFact/FunFact';
import WorksSection        from '@react/components/WorksSection/WorksSection';
import CtaVideoSection     from '@react/components/CtaVideoSection/CtaVideoSection';
import ProcessSectionS2    from '@react/components/ProcessSectionS2/ProcessSectionS2';
import TestimonialSectionS3 from '@react/components/TestimonialSectionS3/TestimonialSectionS3';
import CtaSectionS2        from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer              from '@react/components/footer/Footer';

import ServiceBg from '@react/img/service/service-bg.jpg';

const About = () => (
    <Fragment>
        <Head title="About Us" />
        <NavbarS2 hclass="header-section-2 style-two" />
        <PageTitle pageTitle="About Printbuka" pagesub="About Us" />
        <About2 hclass="about-section section-padding" />
        <MarqueeSection hclass="marquee-section" />
        <ServiceSection hclass="service-section bg-cover section-padding" Bg={ServiceBg} />
        <FunFact hclass="counter-section fix section-padding" />
        <WorksSection hclass="about-feature-section fix section-padding pt-0 bg-cover" eclass="about-feature-wrapper style-2" />
        <CtaVideoSection />
        <ProcessSectionS2 />
        <TestimonialSectionS3 />
        <CtaSectionS2 />
        <Footer />
    </Fragment>
);

export default About;
