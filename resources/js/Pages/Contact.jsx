import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';

import NavbarS2    from '@react/components/NavbarS2/NavbarS2';
import PageTitle   from '@react/components/pagetitle/PageTitle';
import Contactpage from '@react/components/Contactpage/Contactpage';
import CtaSectionS2 from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer      from '@react/components/footer/Footer';

const Contact = ({ flash = {} }) => (
    <Fragment>
        <Head title="Contact Us" />
        <NavbarS2 hclass="header-section-2 style-two" />
        <PageTitle pageTitle="Contact Printbuka" pagesub="Contact" />
        {flash.success && (
            <div className="container pt-4">
                <div className="alert alert-success">{flash.success}</div>
            </div>
        )}
        {flash.error && (
            <div className="container pt-4">
                <div className="alert alert-danger">{flash.error}</div>
            </div>
        )}
        <Contactpage />
        <CtaSectionS2 />
        <Footer />
    </Fragment>
);

export default Contact;
