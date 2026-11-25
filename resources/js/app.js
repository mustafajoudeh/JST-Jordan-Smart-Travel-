import "./bootstrap";

// const page = document.body.dataset.page;

// if (page === "discover") {
//     import("./pages/discover.js");
// }

// if (page === "destinations") {
//     import("./pages/destinations.js");
// }

// if (page === "trip-planner") {
//     import("./pages/trip-planner.js");
// }

// if (page === "admin-reports") {
//     import("./pages/admin-reports.js");
// }

import {
    createIcons,
    Activity,
    Briefcase,
    Building2,
    CalendarDays,
    ChevronDown,
    ClipboardCheck,
    CreditCard,
    FileText,
    FolderOpen,
    Image,
    LayoutDashboard,
    List,
    Map,
    MapPin,
    Menu,
    Route,
    Settings,
    ShieldCheck,
    Sparkles,
    Store,
    Tags,
    User,
    Users,
    Wallet,
} from "lucide";

createIcons({
    icons: {
        Activity,
        Briefcase,
        Building2,
        CalendarDays,
        ChevronDown,
        ClipboardCheck,
        CreditCard,
        FileText,
        FolderOpen,
        Image,
        LayoutDashboard,
        List,
        Map,
        MapPin,
        Menu,
        Route,
        Settings,
        ShieldCheck,
        Sparkles,
        Store,
        Tags,
        User,
        Users,
        Wallet,
    },

    attrs: {
        "stroke-width": 1.75,
    },
});
