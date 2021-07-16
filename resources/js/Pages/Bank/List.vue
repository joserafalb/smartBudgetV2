<template>
    <div>
        <!-- <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
                <v-card-title class="text-h5"
                    >Are you sure you want to delete this item?</v-card-title
                >
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeDelete"
                        >Cancel</v-btn
                    >
                    <v-btn color="blue darken-1" text @click="deleteItemConfirm"
                        >OK</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog> -->
        <v-data-table
            :server-items-length="data.total"
            :loading="isLoading"
            :headers="headers"
            :items="data.data"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            class="elevation-1"
            :page.sync="page"
            :items-per-page="20"
            hide-default-footer
        >
            <template v-slot:top>
                <v-toolbar dense>
                    <v-toolbar-title>Bank List</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        solo
                        prepend-inner-icon="mdi-magnify"
                        hide-details
                        clearable
                        dense
                        label="Search..."
                        @change="refreshSearch(false)"
                        @keydown.enter="refreshSearch(false)"
                        @click:clear="refreshSearch(false)"
                    ></v-text-field>
                    <v-btn color="primary" dark class="ml-2">
                        New Item
                    </v-btn>
                </v-toolbar>
            </template>
            <template v-slot:item.active="{ item }">
                <v-simple-checkbox
                    v-model="item.active === 1 ? true : false"
                    disabled
                ></v-simple-checkbox>
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
        <v-pagination v-model="page" :length="data.last_page"></v-pagination>
    </div>
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
    data() {
        return {
            sortBy: this.$page.props.options.sortBy,
            sortDesc: this.$page.props.options.sortDesc ? true : false,
            search: this.$page.props.options.search,
            page: this.data.current_page,
            isLoading: false,
            isSearch: false,
            headers: [
                { text: "Bank Name", value: "name" },
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
    },
    watch: {
        sortBy: {
            handler() {
                this.getData();
            },

            deep: true
        },
        page: {
            handler() {
                this.getData();
            },

            deep: true
        },
        search: function() {
            this.refreshSearch();
        }
    },
    mounted() {
        this.$once(
            "hook:destroyed",
            this.$inertia.on("start", event => {
                this.isLoading = true;
            })
        );
    },
    methods: {
        getData() {
            this.$inertia.get(
                route("bank.list"),
                {
                    page: this.page,
                    search: this.search,
                    sortBy: this.sortBy,
                    sortDesc: this.sortDesc,
                },
                {
                    only: ["data", "options"]
                }
            );
        },
        refreshSearch(wait = true) {
            if (!this.isSearch) {
                setTimeout(
                    () => {
                        this.getData();
                        this.isSearch = false;
                    },
                    wait ? 1000 : 0
                ); // 1 sec delay
            }
            this.isSearch = true;
        }
    }
};
</script>
