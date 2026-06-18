import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';

import NavbarS2  from '@react/components/NavbarS2/NavbarS2';
import PageTitle from '@react/components/pagetitle/PageTitle';
import BlogList  from '@react/components/BlogList/BlogList';
import CtaSectionS2 from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer    from '@react/components/footer/Footer';

const BlogIndex = ({ posts = [] }) => (
    <Fragment>
        <Head title="Blog" />
        <NavbarS2 hclass="header-section-2 style-two" />
        <PageTitle pageTitle="Print Tips & Inspiration" pagesub="Blog" />
        <BlogList posts={posts} />
        <CtaSectionS2 />
        <Footer />
    </Fragment>
);

export default BlogIndex;
