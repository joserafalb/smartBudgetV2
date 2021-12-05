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
            label="Name"
            required
            :error-messages="nameErrors"
            @input="$v.name.$touch()"
            @blur="$v.name.$touch()"
        />
        <span>
            Color
        </span>
        <v-row>
            <v-col class="shrink" style="min-width: 220px;">
                <v-text-field
                    v-model="color"
                    v-mask="mask"
                    hide-details
                    class="ma-0 pa-0"
                    solo
                >
                    <template v-slot:append>
                        <v-menu
                            v-model="menu"
                            top
                            nudge-bottom="105"
                            nudge-left="16"
                            :close-on-content-click="false"
                        >
                            <template v-slot:activator="{ on }">
                                <div :style="swatchStyle" v-on="on" />
                            </template>
                            <v-card>
                                <v-card-text class="pa-0">
                                    <v-color-picker v-model="color" flat />
                                </v-card-text>
                            </v-card>
                        </v-menu>
                    </template>
                </v-text-field>
            </v-col>
        </v-row>
        <v-checkbox v-model="active" label="Enabled" />
    </edit-page>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import { validationMixin } from "vuelidate";
import { required, maxLength } from "vuelidate/lib/validators";
import EditPage from "../../Components/EditPage.vue";

const routeName = "category-type";

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
            color: this.$page.props.item?.color || "#000",
            ask: "!#XXXXXXXX",
            menu: false
        };
    },
    computed: {
        swatchStyle() {
            const { color, menu } = this;
            return {
                backgroundColor: color,
                cursor: "pointer",
                height: "30px",
                width: "30px",
                borderRadius: menu ? "50%" : "4px",
                transition: "border-radius 200ms ease-in-out"
            };
        },
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
            return this.id ? "Update category type" : "Add a new category type";
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
                    color: this.color,
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
