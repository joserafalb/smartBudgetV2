<template>
    <edit-page
        :title="title"
        :errorMessage="errorMessage"
        :disableSave="disableSave"
        @save="save"
        @goBack="goBack"
    >
        <v-text-field
            v-model="name"
            label="Goal name"
            required
            :error-messages="nameErrors"
            @input="$v.name.$touch()"
            @blur="$v.name.$touch()"
        />
        <v-text-field
            v-model="amount"
            label="Amount"
            prefix="$"
            required
            :error-messages="amountErrors"
            @input="$v.amount.$touch()"
            @blur="$v.amount.$touch()"
        />

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
                    v-model="date"
                    label="Date to complete this goal"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                ></v-text-field>
            </template>
            <v-date-picker
                :min="minDate"
                v-model="date"
                @input="datePicker = false"
            ></v-date-picker>
        </v-menu>

        <v-checkbox v-model="active" label="Enabled" />
    </edit-page>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import { validationMixin } from "vuelidate";
import { required, minValue, decimal, maxLength } from "vuelidate/lib/validators";
import EditPage from "../../Components/EditPage.vue";

const routeName = "goal";

export default {
    layout: DashboardLayout,
    components: {
        EditPage
    },
    data() {
        const current = new Date();
        const minDate = current.getFullYear()+'-'+String(current.getMonth()+1).padStart(2,'0')+'-'+current.getDate();
        return {
            minDate: minDate,
            datePicker: false,
            isValid: false,
            isSaving: false,
            name: this.$page.props.item?.name,
            amount: this.$page.props.item?.amount,
            date: this.$page.props.item?.date,
            active: this.$page.props.item?.active,
            errorMessage: {},
            id: this.$page.props.item?.id
        };
    },
    computed: {
        nameErrors() {
            const errors = [];
            if (!this.$v.name.$dirty) return errors;
            !this.$v.name.maxLength &&
                errors.push("Name must be at most 250 characters long");
            !this.$v.name.required && errors.push("Name is required.");
            return errors;
        },
        amountErrors() {
            const errors = [];
            if (!this.$v.amount.$dirty) return errors;
            !this.$v.amount.required && errors.push("Name is required.");
            !this.$v.amount.minValue && errors.push("Must be grather than 1.");
            !this.$v.amount.decimal && errors.push("Must be a valid amount");
            return errors;
        },
        disableSave() {
            return this.$v.$invalid || this.isSaving;
        },
        title() {
            return this.id ? "Update goal" : "Add a new goal";
        }
    },
    methods: {
        goBack() {
            this.$inertia.get(route(routeName + ".index"));
        },
        save(redirect) {
            // Clear errors
            this.$v.$reset();

            // Check for errors
            this.$v.$touch();
            this.isSaving = true;

            const urlAction = this.id ? ".update" : ".store";
            axios({
                url: route(routeName + urlAction, this.id),
                method: this.id ? "PUT" : "POST",
                data: {
                    name: this.name,
                    amount: this.amount,
                    date: this.date,
                    active: this.active,
                    id: this.id
                }
            })
                .then(response => {
                    // If we are adding and not going back, we need to set the id to enter in update mode
                    if (redirect) {
                        this.goBack();
                    } else if (urlAction === ".store") {
                        this.id = response.data;
                    }
                })
                .catch(error => {
                    this.errorMessage = error.response?.data.errors || [];
                })
                .then(() => {
                    this.isSaving = false;
                });
        }
    },
    mixins: [validationMixin],
    validations: {
        name: { required, maxLength: maxLength(250) },
        amount: {required, minValue: minValue(1), decimal}
    }
};
</script>
