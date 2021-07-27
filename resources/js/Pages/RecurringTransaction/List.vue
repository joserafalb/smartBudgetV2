<template>
    <list-page
        :headers="headers"
        route-name="recurring-transaction"
        :data="data"
        title="Recurring Transaction List"
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
                { text: "Name", value: "name" },
                { text: "Starts", value: "start_date" },
                { text: "Ends", value: "end_date" },
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
