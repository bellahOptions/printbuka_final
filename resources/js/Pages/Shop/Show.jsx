import React, { Fragment } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { connect } from 'react-redux';
import { addToCart } from '@react/store/actions/action';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import NavbarS2    from '@react/components/NavbarS2/NavbarS2';
import PageTitle   from '@react/components/pagetitle/PageTitle';
import ProductComponent from '@react/main-component/ShopSinglePage/product';
import ProductTabs from '@react/main-component/ShopSinglePage/alltab';
import CtaSectionS2 from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer      from '@react/components/footer/Footer';

const ShopShow = ({ addToCart, product = null, relatedProducts = [] }) => {
    return (
        <Fragment>
            <Head title={product ? product.title : 'Product'} />
            <NavbarS2 hclass="header-section-2 style-two" />
            <PageTitle pageTitle={product ? product.title : 'Product'} pagesub="Product Detail" />
            <section className="product-details-section section-padding section-bg-2">
                <div className="container">
                    <div className="product-details-wrapper">
                        {product && (
                            <ProductComponent item={product} addToCart={addToCart} />
                        )}
                        <ProductTabs />
                    </div>
                </div>
            </section>
            <CtaSectionS2 />
            <Footer />
            <ToastContainer />
        </Fragment>
    );
};

export default connect(null, { addToCart })(ShopShow);
