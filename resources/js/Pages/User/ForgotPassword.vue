<template>
    <web-layout title="Password Reset">
        <v-alert
            v-if="isSent"
            class="mx-4"
            outlined
            type="success"
            border="left"
            >Reset instructions sent to your email</v-alert
        >
        <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation>
                <v-text-field
                    v-model="form.email"
                    :rules="emailRules"
                    label="E-mail"
                    required
                />
            </v-form>
        </v-card-text>

        <v-card-actions class="d-flex flex-column">
            <v-btn
                :disabled="!valid"
                color="primary"
                class="mx-auto px-4 mb-4"
                @click="reset"
            >
                Email Password Reset Link
            </v-btn>
            <inertia-link href="/">Login</inertia-link>
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
            isSent: false,
            valid: true,
            form: this.$inertia.form({
                email: ""
            }),
            emailRules: [
                v => !!v || "E-mail is required",
                v => /.+@.+\..+/.test(v) || "E-mail must be valid"
            ]
        };
    },

    methods: {
        reset() {
            this.form.post("/forgot-password", {
                onFinish: () => (this.isSent = true)
            });
        }
    }
};
</script>
