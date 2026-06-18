import React, { Fragment } from 'react';
import { Head } from '@inertiajs/react';
import { connect } from 'react-redux';
import { addToCart } from '@react/store/actions/action';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import Navbar        from '@react/components/Navbar/Navbar';
import Hero          from '@react/components/hero/hero';
import FeatureSection from '@react/components/FeatureSection/FeatureSection';
import About         from '@react/components/about/about';
import MarqueeSection from '@react/components/MarqueeSection/MarqueeSection';
import ServiceSection from '@react/components/ServiceSection/ServiceSection';
import FunFact        from '@react/components/FunFact/FunFact';
import ProcessSection from '@react/components/ProcessSection/ProcessSection';
import Testimonial    from '@react/components/Testimonial/Testimonial';
import CtaSection     from '@react/components/CtaSection/CtaSection';
import Footer         from '@react/components/footer/Footer';

import ServiceBg from '@react/img/service/service-bg.jpg';

// Shop product strip — only rendered when products passed from Laravel
import ProductSection from '@react/components/ProductSection/ProductSection';

const Home = ({ addToCart, shopProducts = [], featuredShopProducts = [] }) => {
    const addToCartProduct = (product, qty = 1) => addToCart(product, qty);

    const products = shopProducts.length
        ? shopProducts
        : featuredShopProducts;

    return (
        <Fragment>
            <Head title="Home" />
            <Navbar hclass="header-section" />
            <Hero />
            <FeatureSection />
            <About />
            <MarqueeSection hclass="marquee-section margin-top-8 mb-80" />
            <ServiceSection hclass="service-section bg-cover section-padding" Bg={ServiceBg} />
            {products.length > 0 && (
                <ProductSection
                    addToCartProduct={addToCartProduct}
                    products={products}
                />
            )}
            <FunFact hclass="counter-section fix section-padding pt-0" />
            <ProcessSection />
            <Testimonial />
            <CtaSection />
            <Footer />
            <ToastContainer />
        </Fragment>
    );
};

const mapStateToProps = () => ({});
export default connect(mapStateToProps, { addToCart })(Home);
