import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';
import { connect } from 'react-redux';
import { addToCart } from '@react/store/actions/action';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import NavbarS2    from '@react/components/NavbarS2/NavbarS2';
import PageTitle   from '@react/components/pagetitle/PageTitle';
import ShopProduct from '@react/components/ShopProduct';
import CtaSectionS2 from '@react/components/CtaSectionS2/CtaSectionS2';
import Footer      from '@react/components/footer/Footer';

const ShopIndex = ({ addToCart, products = [] }) => {
    const addToCartProduct = (product, qty = 1) => addToCart(product, qty);

    return (
        <Fragment>
            <Head title="Shop" />
            <NavbarS2 hclass="header-section-2 style-two" />
            <PageTitle pageTitle="Print Products" pagesub="Shop" />
            <ShopProduct
                addToCartProduct={addToCartProduct}
                products={products}
            />
            <CtaSectionS2 />
            <Footer />
            <ToastContainer />
        </Fragment>
    );
};

export default connect(null, { addToCart })(ShopIndex);
