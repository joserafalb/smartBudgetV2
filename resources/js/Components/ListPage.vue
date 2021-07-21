<template>
    <div>
        <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
                <v-card-title
                    >Are you sure you want to delete this item?</v-card-title
                >
                <v-card-text>
                    {{ itemToDelete.name }}
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="dialogDelete = false">No</v-btn>
                    <v-spacer/>
                    <v-btn color="primary" @click="deleteItemConfirm">Yes, delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
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
                    <v-toolbar-title>{{ title }} </v-toolbar-title>
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
                    <v-btn color="primary" dark class="ml-2" @click="newItem">
                        New Item
                    </v-btn>
                </v-toolbar>
            </template>
            <template v-slot:item.active="{ item }">
                <v-simple-checkbox
                    color="green"
                    v-model="item.active === 1 ? true : false"
                ></v-simple-checkbox>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2 edit-icon" @click="editItem(item)">
                    mdi-pencil
                </v-icon>
                <v-icon small class="delete-icon" @click="deleteItem(item)">
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
export default {
    data() {
        return {
            itemToDelete: {},
            dialogDelete: false,
            sortBy: this.$page.props.options.sortBy,
            sortDesc: this.$page.props.options.sortDesc ? true : false,
            search: this.$page.props.options.search,
            page: this.data.current_page,
            isLoading: false,
            isSearch: false
        };
    },
    props: {
        data: {
            type: Object,
            required: true
        },
        headers: {
            type: Array,
            required: true
        },
        routeName: {
            type: String,
            required: true
        },
        title: {
            type: String,
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
        sortDesc: {
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
        newItem() {
            this.$inertia.visit(route(this.routeName + ".create"));
        },
        editItem(item) {
            this.$inertia.visit(route(this.routeName + ".edit", item.id));
        },
        deleteItem(item) {
            this.itemToDelete = item;
            this.dialogDelete = true;
        },
        getData(resetPage = false) {
            const params = {
                page: resetPage ? 1 : this.page
            };

            if (this.search) {
                params.search = this.search;
            }

            if (this.sortBy) {
                params.sortBy = this.sortBy;
            }

            if (this.sortDesc) {
                params.sortDesc = true;
            }

            this.$inertia.get(route(this.routeName + ".index"), params, {
                only: ["data", "options"]
            });
        },
        refreshSearch(wait = true) {
            if (!this.isSearch) {
                setTimeout(
                    () => {
                        this.getData(true);
                        this.isSearch = false;
                    },
                    wait ? 1000 : 0
                ); // 1 sec delay
            }
            this.isSearch = true;
        },
        deleteItemConfirm() {
            // Make request to delete from database
            axios
                .delete(
                    route(this.routeName + ".destroy", this.itemToDelete.id)
                )
                .then(() => {
                    this.getData();
                });

            this.dialogDelete = false;
        }
    }
};
</script>

<style scoped>
.delete-icon:hover {
    color: red;
}

.edit-icon:hover {
    color: blue;
}
</style>
