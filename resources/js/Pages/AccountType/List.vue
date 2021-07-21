<template>
    <list-page
        :headers="headers"
        route-name="account-type"
        :data="data"
        title="Account Type"
    />
</template>

<script>
import ListPage from "@/Components/ListPage";
import DashboardLayout from "@/Layouts/Dashboard";

export default {
    layout: DashboardLayout,

    components: {
        ListPage
    },
    data() {
        return {
            headers: [
                { text: "Account Type", value: "name" },
                { text: "Active", value: "active" },
                { text: "Actions", value: "actions", sortable: false }
            ]
        };
    },
    props: {
        data: {
            type: Object,
            required: true
        }
    }
};
</script>
