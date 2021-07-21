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
            label="Category name"
            required
            :error-messages="nameErrors"
            @input="$v.fields.name.$touch()"
            @blur="$v.fields.name.$touch()"
        />
        <v-autocomplete
            v-model="fields.categoryTypeId"
            :items="categoryTypes"
            item-text="name"
            item-value="id"
            label="Type"
            :error-messages="categoryTypeErrors"
            @input="$v.fields.categoryTypeId.$touch()"
            @blur="$v.fields.categoryTypeId.$touch()"
        ></v-autocomplete>
        <v-checkbox v-model="fields.active" label="Is active?" />
    </edit-page>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import { validationMixin } from "vuelidate";
import { required, maxLength } from "vuelidate/lib/validators";
import EditPage from "../../Components/EditPage.vue";

const routeName = "category";

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
                categoryTypeId: this.$page.props.item?.category_type_id,
                active: this.$page.props.item?.active
            },
            errorMessage: {},
        };
    },
    props: {
        categoryTypes: {
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
        categoryTypeErrors() {
            const errors = [];
            if (!this.$v.fields.categoryTypeId.$dirty) return errors;
            !this.$v.fields.categoryTypeId.required && errors.push("Type is required.");
            return errors;
        },
        disableSave() {
            return this.$v.$invalid || this.isSaving;
        },
        title() {
            return this.fields.id ? "Update category" : "Add a new category";
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

            const urlAction = this.fields.id ? ".update" : ".store";
            axios({
                url: route(routeName + urlAction, this.fields.id),
                method: this.fields.id ? "PUT" : "POST",
                data: {
                    item: this.fields
                }
            })
                .then(response => {
                    // If we are adding and not going back, we need to set the id to enter in update mode
                    if (redirect) {
                        this.goBack();
                    } else if (urlAction === ".store") {
                        this.fields.id = response.data;
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
            categoryTypeId: { required },
        }
    }
};
</script>
