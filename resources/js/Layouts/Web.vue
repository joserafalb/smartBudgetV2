<template>
    <v-app>
        <v-card class="mx-auto my-12" max-width="374">
            <template slot="progress">
                <v-progress-linear
                    color="deep-purple"
                    height="10"
                    indeterminate
                ></v-progress-linear>
            </template>

            <v-img height="250" contain src="/images/SmartBudget.png"></v-img>

            <v-card-title>{{ title }}</v-card-title>
            <v-alert
                class="mx-4"
                v-if="errorMessage"
                outlined
                type="error"
                border="left"
                >{{ errorMessage }}
            </v-alert>

            <slot></slot>
        </v-card>
    </v-app>
</template>

<script>
export default {
    props: {
        title: { type: String, required: true },
        errorMessage: { type: String, default: "" }
    }
};
</script>
