import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';

import NavbarS2   from '@react/components/NavbarS2/NavbarS2';
import PageTitle  from '@react/components/pagetitle/PageTitle';
import BlogSingle from '@react/components/BlogDetails/BlogSingle';
import CtaSectionS2 from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer     from '@react/components/footer/Footer';

const BlogShow = ({ post = null }) => {
    const title = post ? post.title : 'Blog Post';

    return (
        <Fragment>
            <Head title={title} />
            <NavbarS2 hclass="header-section-2 style-two" />
            <PageTitle pageTitle={title} pagesub="Blog" />
            <BlogSingle post={post} />
            <CtaSectionS2 />
            <Footer />
        </Fragment>
    );
};

export default BlogShow;
