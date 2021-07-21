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
            label="Bank name"
            required
            :error-messages="nameErrors"
            @input="$v.name.$touch()"
            @blur="$v.name.$touch()"
        />
        <v-checkbox v-model="active" label="Is active?" />
    </edit-page>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import { validationMixin } from "vuelidate";
import { required, maxLength } from "vuelidate/lib/validators";
import EditPage from "../../Components/EditPage.vue";

const routeName = "banks";

export default {
    layout: DashboardLayout,
    components: {
        EditPage
    },
    data() {
        return {
            isValid: false,
            isSaving: false,
            name: this.$page.props.item?.name,
            active: this.$page.props.item?.active,
            errorMessage: {},
            id: this.$page.props.item?.id,
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
        disableSave() {
            return this.$v.$invalid || this.isSaving;
        },
        title() {
            return this.id ? "Update bank" : "Add a new bank";
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
        name: { required, maxLength: maxLength(250) }
    }
};
</script>
