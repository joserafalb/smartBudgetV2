<template>
    <web-layout title="Register" :errorMessage="form.errors.email">
        <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation>
                <v-text-field v-model="form.name" label="Full name" required />
                <v-text-field
                    v-model="form.email"
                    :rules="emailRules"
                    label="E-mail"
                    required
                />
                <v-text-field
                    v-model="form.password"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :rules="[rules.required, rules.min]"
                    :type="showPassword ? 'text' : 'password'"
                    name="newpassword"
                    label="Enter a new Password"
                    counter
                    @click:append="showPassword = !showPassword"
                ></v-text-field>
            </v-form>
        </v-card-text>

        <v-card-actions class="d-flex flex-column">
            <v-btn
                :disabled="!valid"
                color="primary"
                class="mx-auto px-4 mb-4"
                @click="register"
            >
                Register
            </v-btn>
            <div>
                Already have an account?
                <inertia-link href="/">Login</inertia-link>
                instead.
            </div>
        </v-card-actions>
    </web-layout>
</template>

<script>
import WebLayout from "@/Layouts/Web.vue";
export default {
    components: {
        WebLayout
    },
    data() {
        return {
            form: this.$inertia.form({
                name: "",
                email: "",
                password: ""
            }),
            showPassword: false,
            valid: true,
            emailRules: [
                v => !!v || "E-mail is required",
                v => /.+@.+\..+/.test(v) || "E-mail must be valid"
            ],
            rules: {
                required: value => !!value || "Required.",
                min: v => v.length >= 8 || "Min 8 characters"
            }
        };
    },
    methods: {
        register() {
            this.form
                .transform(data => ({
                    ...data,
                    password_confirmation: data.password
                }))
                .post("/register");
        }
    }
};
</script>
