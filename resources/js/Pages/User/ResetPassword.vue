<template>
    <web-layout title="Password Reset">
        <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation>
                <v-text-field
                    v-model="password"
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
                @click="reset"
            >
                Save
            </v-btn>
            <inertia-link href="/">Login</inertia-link>
        </v-card-actions>
    </web-layout>
</template>

<script>
import WebLayout from "@/Layouts/Web.vue";
export default {
    components: {
        WebLayout
    },
    data: () => ({
        showPassword: false,
        valid: true,
        password: "",
        rules: {
            required: value => !!value || "Required.",
            min: v => v.length >= 8 || "Min 8 characters"
        }
    }),
    props: {
        email: { type: String, required: true },
        token: { type: String, required: true }
    },
    methods: {
        reset() {
            this.$inertia.post("/reset-password", {
                token: this.token,
                email: this.email,
                password: this.password,
                password_confirmation: this.password
            });
        }
    }
};
</script>
