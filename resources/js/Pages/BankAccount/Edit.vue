<template>
    <edit-page
        :title="title"
        :errorMessage="errorMessage"
        :disableSave="disableSave"
        @save="save"
        @goBack="goBack"
    >
        <v-text-field
            v-model="fields.name"
            label="Name"
            required
            :error-messages="nameErrors"
            @input="$v.fields.name.$touch()"
            @blur="$v.fields.name.$touch()"
        />
        <v-autocomplete
            v-model="fields.bankId"
            :items="banks"
            item-text="name"
            item-value="id"
            label="Bank"
            :error-messages="bankErrors"
            @input="$v.fields.bankId.$touch()"
            @blur="$v.fields.bankId.$touch()"
        ></v-autocomplete>
        <v-autocomplete
            v-model="fields.accountTypeId"
            :items="accountTypes"
            item-text="name"
            item-value="id"
            label="Account Type"
            :error-messages="accountTypeErrors"
            @input="$v.fields.accountTypeId.$touch()"
            @blur="$v.fields.accountTypeId.$touch()"
        ></v-autocomplete>
        <v-text-field
            v-if="isCredit"
            v-model="fields.creditLimit"
            label="Credit limit"
        />

        <v-checkbox v-model="fields.active" label="Is active?" />
    </edit-page>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import { validationMixin } from "vuelidate";
import { required, maxLength } from "vuelidate/lib/validators";
import EditPage from "../../Components/EditPage.vue";

const routeName = "bank-accounts";

export default {
    layout: DashboardLayout,
    components: {
        EditPage
    },
    data() {
        return {
            isValid: false,
            isSaving: false,
            fields: {
                id: this.$page.props.item?.id,
                name: this.$page.props.item?.name,
                bankId: this.$page.props.item?.bank_id,
                accountTypeId: this.$page.props.item?.account_type_id,
                creditLimit: this.$page.props.item?.credit_limit,
                active: this.$page.props.item?.active
            },
            errorMessage: {},
            id: this.$page.props.item?.id
        };
    },
    props: {
        banks: {
            type: Array,
            required: true
        },
        accountTypes: {
            type: Array,
            required: true
        }
    },
    computed: {
        isCredit() {
            return this.accountTypes.filter(
                item => item.id === this.fields.accountTypeId
            )[0]?.isCredit;
        },
        nameErrors() {
            const errors = [];
            if (!this.$v.fields.name.$dirty) return errors;
            !this.$v.fields.name.maxLength &&
                errors.push("Name must be at most 250 characters long");
            !this.$v.fields.name.required && errors.push("Name is required.");
            return errors;
        },
        bankErrors() {
            const errors = [];
            if (!this.$v.fields.bankId.$dirty) return errors;
            !this.$v.fields.bankId.required && errors.push("Bank is required.");
            return errors;
        },
        accountTypeErrors() {
            const errors = [];
            if (!this.$v.fields.accountTypeId.$dirty) return errors;
            !this.$v.fields.accountTypeId.required &&
                errors.push("Account Type is required.");
            return errors;
        },
        disableSave() {
            return this.$v.$invalid || this.isSaving;
        },
        title() {
            return this.id ? "Update bank account" : "Add a new bank account";
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
                    item: this.fields
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
        fields: {
            name: { required, maxLength: maxLength(250) },
            bankId: { required },
            accountTypeId: { required }
        }
    }
};
</script>
