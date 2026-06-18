import React from 'react';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { Provider } from 'react-redux';
import store from './store/index';

// Template-wide styles (Bootstrap + template SCSS)
import '@react/css/bootstrap.min.css';
import '@react/css/all.min.css';
import '@react/css/animate.css';
import '@react/css/nice-select.css';
import '@react/css/main.css';
import '@react/style.scss';

class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { error: null };
    }
    static getDerivedStateFromError(error) {
        return { error };
    }
    componentDidCatch(error, info) {
        console.error('[Printbuka] React render error:', error, info);
    }
    render() {
        if (this.state.error) {
            return (
                <div style={{ padding: 40, fontFamily: 'monospace', background: '#fff' }}>
                    <h2 style={{ color: '#c00' }}>Page Error</h2>
                    <pre style={{ color: '#333', whiteSpace: 'pre-wrap' }}>{String(this.state.error)}</pre>
                    <pre style={{ color: '#666', fontSize: 12, whiteSpace: 'pre-wrap' }}>{this.state.error?.stack}</pre>
                </div>
            );
        }
        return this.props.children;
    }
}

createInertiaApp({
    title: (title) => `${title} | Printbuka`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const page = pages[`./Pages/${name}.jsx`];
        if (!page) throw new Error(`Inertia page not found: ./Pages/${name}.jsx`);
        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <ErrorBoundary>
                <Provider store={store}>
                    <App {...props} />
                </Provider>
            </ErrorBoundary>
        );
    },
});
