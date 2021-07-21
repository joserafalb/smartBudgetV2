<template>
    <list-page
        :headers="headers"
        route-name="bank-accounts"
        :data="data"
        title="Bank Account List"
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
                { text: "Account", value: "name" },
                { text: "Bank", value: "bank" },
                { text: "Type", value: "account_type" },
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
