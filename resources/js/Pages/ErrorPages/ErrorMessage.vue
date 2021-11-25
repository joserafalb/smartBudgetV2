<template>
    <v-row>
        <v-col>
            <v-card>
                <v-card-title>
                    {{ error.message }}
                </v-card-title>
                <v-card-text>
                    <inertia-link :href="error.tip.link">
                        {{ error.tip.message }}
                    </inertia-link>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";

export default {
    layout: DashboardLayout,

    /**
     * Define the properties passed into the component from the parent.
     *
     * @link https://vuejs.org/v2/guide/components.html#Props
     */
    props: {
        error: {
            type: Object,
            required: true
        }
    }
};
</script>

<style></style>
