<template>
    <v-card class="editPageCard">
        <v-card-title>{{ title }}</v-card-title>
        <v-card-text>
            <v-alert
                outlined
                type="error"
                border="left"
                v-if="Object.keys(errorMessage).length"
            >
                <span v-for="(message, index) in errorMessage" :key="index">
                    {{ message[0] }}
                </span>
            </v-alert>
            <v-form>
                <slot></slot>
            </v-form>
        </v-card-text>
        <v-card-actions>
            <v-spacer />
            <v-btn @click="$emit('goBack')" class="mr-4">
                Back
            </v-btn>
            <v-btn
                color="secondary"
                class=""
                @click="$emit('save', false)"
                :disabled="disableSave"
            >
                {{ buttonTitle }} and continue
            </v-btn>
            <v-btn
                color="primary"
                @click="$emit('save', true)"
                :disabled="disableSave"
            >
                {{ buttonTitle }}
            </v-btn>
            <v-spacer />
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            item: this.$page.props.item
        };
    },
    props: {
        title: {
            type: String,
            required: true
        },
        errorMessage: {
            type: Object,
            default: []
        },
        disableSave: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        buttonTitle() {
            return this.title.split(" ")[0];
        }
    }
};
</script>

<style lang="postcss" scoped>
.editPageCard {
    @apply tw-max-w-md tw-m-auto;
}
</style>
