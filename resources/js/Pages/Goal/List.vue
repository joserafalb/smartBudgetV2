<template>
    <list-page
        :headers="headers"
        route-name="goal"
        :data="data"
        title="Goal List"
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
                { text: "Amount", value: "amount" },
                { text: "Date to complete", value: "date" },
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
