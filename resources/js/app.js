import './bootstrap';
import {
    Activity,
    ArrowDownRight,
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    BriefcaseBusiness,
    Building2,
    CalendarDays,
    CalendarRange,
    Check,
    ChartNoAxesCombined,
    ChevronDown,
    ChevronRight,
    CircleAlert,
    Compass,
    CreditCard,
    Database,
    Download,
    Ellipsis,
    Eye,
    EyeOff,
    FolderOpen,
    Globe2,
    LayoutDashboard,
    List,
    LockKeyhole,
    LogOut,
    Map,
    MapPin,
    Menu,
    MoveUpRight,
    RotateCw,
    Route,
    ShieldCheck,
    Settings,
    Tags,
    Sparkles,
    TicketCheck,
    UserRound,
    Users,
    WalletCards,
    WifiOff,
    X,
    createIcons,
} from 'lucide';

createIcons({
    icons: {
        Activity,
        ArrowDownRight,
        ArrowLeft,
        ArrowRight,
        ArrowUpRight,
        BriefcaseBusiness,
        Building2,
        CalendarDays,
        CalendarRange,
        Check,
        ChartNoAxesCombined,
        ChevronDown,
        ChevronRight,
        CircleAlert,
        Compass,
        CreditCard,
        Database,
        Download,
        Ellipsis,
        Eye,
        EyeOff,
        FolderOpen,
        Globe2,
        LayoutDashboard,
        List,
        LockKeyhole,
        LogOut,
        Map,
        MapPin,
        Menu,
        MoveUpRight,
        RotateCw,
        Route,
        ShieldCheck,
        Settings,
        Tags,
        Sparkles,
        TicketCheck,
        UserRound,
        Users,
        WalletCards,
        WifiOff,
        X,
    },
    attrs: { 'stroke-width': 1.75 },
});

const page = document.body.dataset.page;

if (page === 'home') {
    import('./pages/home').then(({ initHome }) => initHome());
}

if (page === 'login') {
    import('./pages/login').then(({ initLogin }) => initLogin());
}

if (page === 'register') {
    import('./pages/register').then(({ initRegister }) => initRegister());
}

if (page === 'admin-dashboard') {
    import('./pages/admin-dashboard').then(({ initAdminDashboard }) => initAdminDashboard());
}

if (page === 'admin-management') {
    import('./pages/admin-management').then(({ initAdminManagement }) => initAdminManagement());
}

if (page === 'workspace') {
    import('./pages/home').then(({ initHome }) => initHome());
}
