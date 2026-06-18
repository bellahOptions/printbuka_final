/**
 * Shims react-router-dom for Inertia.
 * The React template uses <Link to="..."> everywhere.
 * Inertia uses <Link href="...">.
 * This shim translates so template components work unchanged.
 */
import { Link as InertiaLink, router } from '@inertiajs/react';

export const Link = ({ to, href, onClick, children, className, ...rest }) => (
    <InertiaLink
        href={to || href || '/'}
        onClick={onClick}
        className={className}
        {...rest}
    >
        {children}
    </InertiaLink>
);

export const useNavigate = () => (path) => router.visit(path);
export const useLocation = () => ({ pathname: window.location.pathname });
export const useParams = () => {
    const parts = window.location.pathname.split('/');
    return { slug: parts[parts.length - 1] };
};

export const BrowserRouter = ({ children }) => <>{children}</>;
export const Routes = ({ children }) => <>{children}</>;
export const Route = () => null;
export const NavLink = Link;
export const Outlet = () => null;
