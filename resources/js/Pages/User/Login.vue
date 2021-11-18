<template>
    <web-layout title="Login" :errorMessage="form.errors.email">
        <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation @keyup.native.enter="login">
                <v-text-field
                    v-model="form.email"
                    :rules="emailRules"
                    label="E-mail"
                    required
                ></v-text-field>
                <v-text-field
                    v-model="form.password"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :rules="[rules.required, rules.min]"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    label="Password"
                    counter
                    @click:append="showPassword = !showPassword"
                ></v-text-field>
                <v-checkbox
                    name="remember"
                    v-model="form.rememberMe"
                    label="Remember me"
                ></v-checkbox>
            </v-form>
        </v-card-text>

        <v-card-actions class="d-flex flex-column">
            <v-btn
                :disabled="!valid || form.processing"
                color="primary"
                class="mx-auto px-4 mb-4"
                @click="login"
            >
                Login
            </v-btn>
            <inertia-link href="/forgot-password"
                >Forgot my password</inertia-link
            >
            <inertia-link href="/register">Register</inertia-link>
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
                email: "",
                password: "",
                rememberMe: false
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
        login() {
            this.form
                .transform(data => ({
                    ...data,
                    remember: data.rememberMe
                }))
                .post("/login");
        }
    }
};
</script>
