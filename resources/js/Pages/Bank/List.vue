<template>
    <v-data-table
        :headers="headers"
        :items="data"
        sort-by="name"
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Bank list</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-btn color="primary" dark class="mb-2">
                    New Item
                </v-btn>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5"
                            >Are you sure you want to delete this
                            item?</v-card-title
                        >
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="blue darken-1"
                                text
                                @click="closeDelete"
                                >Cancel</v-btn
                            >
                            <v-btn
                                color="blue darken-1"
                                text
                                @click="deleteItemConfirm"
                                >OK</v-btn
                            >
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">
                mdi-pencil
            </v-icon>
            <v-icon small @click="deleteItem(item)">
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            No records found.
        </template>
    </v-data-table>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";

export default {
    // Using a render function
    layout: (h, page) => h(DashboardLayout, [page]),

    // Using the shorthand
    layout: DashboardLayout,

    methods: {
        deleteItemConfirm() {
            this.closeDelete();
        },
        closeDelete() {
            this.dialogDelete = false;
        },
        editItem(item) {},
        deleteItem(item) {}
    },
    /**
     * You must define reactive data for all Vue models and Vue template data.
     *
     * @link https://vuejs.org/v2/api/#data
     *
     * @return Object Vue model and template binding defaults.
     */
    data() {
        return {
            dialogDelete: false,
            headers: [
                { text: "Bank Name", value: "name" },
                { text: "Actions", value: "actions", sortable: false }
            ],
            data: [{ name: "Chase" }, { name: "Wells Fargo" }]
        };
    }
};
</script>
