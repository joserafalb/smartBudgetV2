<template>
    <v-dialog v-model="dialog" persistent>
        <v-card class="editPageCard">
            <v-card-title class="text-h5">
                {{ title }}
                <v-spacer />
                <v-btn color="red" @click="$emit('delete', defaultItem)">
                    Delete
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-menu
                    v-model="datePicker"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                            v-model="defaultItem.date"
                            label="Date"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="defaultItem.date"
                        @input="datePicker = false"
                    ></v-date-picker>
                </v-menu>
                <v-autocomplete
                    v-model="defaultItem.categoryId"
                    :items="categories"
                    item-text="name"
                    item-value="id"
                    label="Category"
                ></v-autocomplete>
                <v-text-field
                    v-model="defaultItem.amount"
                    label="Amount"
                    required
                    type="number"
                />
                <v-text-field
                    v-model="defaultItem.description"
                    label="Description"
                    required
                />
                <v-slider
                    label="Status"
                    v-model="defaultItem.transactionStatus"
                    :tick-labels="statusLabels"
                    :max="2"
                    step="1"
                    ticks="always"
                    tick-size="2"
                ></v-slider>
            </v-card-text>
            <v-card-actions>
                <v-btn color="green darken-1" text @click="$emit('cancel')">
                    Cancel
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    color="green darken-1"
                    text
                    @click="$emit('save', defaultItem)"
                >
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        dialog: { type: Boolean, default: false },
        id: { type: Number, required: false },
        categories: { type: Array, required: true },
        defaultDate: { type: String, default: "" },
        selectedItem: { type: Object, required: true }
    },
    data() {
        return {
            datePicker: false,
            defaultItem: {},
            statusLabels: ["Pending", "Processing", "Processed"]
        };
    },
    computed: {
        title() {
            return this.id ? "Update Transaction" : "Add Transaction";
        }
    },
    watch: {
        dialog() {
            this.defaultItem = this.selectedItem;
        }
    }
};
</script>

<style lang="postcss" scoped>
.editPageCard {
    @apply tw-max-w-md tw-m-auto;
}
</style>
