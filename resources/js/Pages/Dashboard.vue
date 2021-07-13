<template>
    <div>
        Dashboard
    </div>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";

export default {
    // Using a render function
    layout: (h, page) => h(DashboardLayout, [page]),

    // Using the shorthand
    layout: DashboardLayout
};
</script>
