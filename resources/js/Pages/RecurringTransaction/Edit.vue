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
            label="Name"
            required
            :error-messages="nameErrors"
            @input="$v.fields.name.$touch()"
            @blur="$v.fields.name.$touch()"
        />
        <v-radio-group v-model="fields.scheduleType">
            <v-radio value="1">
                <template v-slot:label>
                    Repeat every
                    <v-text-field
                        v-model="dayRepeat"
                        hide-details="auto"
                        dense
                        class="mx-3 col-md-1"
                        type="number"
                        min="1"
                        :error-messages="dayRepeatErrors"
                        @input="$v.dayRepeat.$touch()"
                        @blur="$v.dayRepeat.$touch()"
                    />
                    days
                </template>
            </v-radio>
            <v-radio value="2">
                <template v-slot:label>
                    Every day of the month
                    <v-text-field
                        v-model="dayOfTheMonth"
                        hide-details="auto"
                        dense
                        class="mx-3 col-md-1"
                        type="number"
                        min="1"
                        max="28"
                    />
                </template>
            </v-radio>
            <v-radio value="3">
                <template v-slot:label>
                    Every last day of the month
                </template>
            </v-radio>
            <v-radio value="4">
                <template v-slot:label>
                    Every day of the week
                    <v-autocomplete
                        v-model="dayOfTheWeek"
                        hide-details="auto"
                        dense
                        class="mx-3 col-md-2"
                        :items="weekDays"
                        label=""
                    ></v-autocomplete>
                </template>
            </v-radio>
        </v-radio-group>
        <v-autocomplete
            v-model="fields.bankAccountId"
            :items="bankAccounts"
            item-text="name"
            item-value="id"
            label="Bank Account"
            :error-messages="bankAccountErrors"
            @input="$v.fields.bankAccountId.$touch()"
            @blur="$v.fields.bankAccountId.$touch()"
        ></v-autocomplete>
        <v-autocomplete
            v-model="fields.categoryId"
            :items="categories"
            item-text="name"
            item-value="id"
            label="Category"
            :error-messages="categoryErrors"
            @input="$v.fields.categoryId.$touch()"
            @blur="$v.fields.categoryId.$touch()"
        ></v-autocomplete>
        <v-text-field
            v-model="fields.description"
            label="Description"
            required
        />
        <v-text-field v-model="fields.amount" label="Amount" required />
        <v-menu
            v-model="startDatePicker"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
        >
            <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="fields.startDate"
                    label="Starting Date"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                ></v-text-field>
            </template>
            <v-date-picker
                v-model="fields.startDate"
                @input="startDatePicker = false"
            ></v-date-picker>
        </v-menu>
        <v-autocomplete
            v-model="fields.limitType"
            :items="limitTypes"
            label="Set limit by"
        ></v-autocomplete>
        <v-menu
            v-if="fields.limitType === 'Date'"
            v-model="endDatePicker"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
        >
            <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="fields.endDate"
                    label="Ending Date"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                ></v-text-field>
            </template>
            <v-date-picker
                v-model="fields.endDate"
                @input="endDatePicker = false"
            ></v-date-picker>
        </v-menu>
        <v-text-field
            v-if="fields.limitType === 'Number of transactions'"
            v-model="fields.quantity"
            label="Transactions quantity"
            required
        />
        <v-checkbox v-model="fields.active" label="Enabled" />
    </edit-page>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import { validationMixin } from "vuelidate";
import {
    required,
    requiredIf,
    maxLength,
    minValue
} from "vuelidate/lib/validators";
import EditPage from "../../Components/EditPage.vue";

const routeName = "recurring-transaction";

export default {
    layout: DashboardLayout,
    components: {
        EditPage
    },
    data() {
        return {
            limitTypes: ["None", "Number of transactions", "Date"],
            weekDays: [
                "Sunday",
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday"
            ],
            dayOfTheWeek: this.$page.props.dayOfTheWeek,
            dayOfTheMonth: this.$page.props.dayOfTheMonth,
            dayRepeat: this.$page.props.dayRepeat,
            startDatePicker: false,
            endDatePicker: false,
            isSaving: false,
            fields: {
                id: this.$page.props.item?.id,
                name: this.$page.props.item?.name,
                bankAccountId: this.$page.props.item?.bank_account_id,
                categoryId: this.$page.props.item?.category_id,
                amount: this.$page.props.item?.amount,
                startDate: this.$page.props.item?.start_date,
                endDate: this.$page.props.item?.end_date,
                active: this.$page.props.item?.active,
                quantity: this.$page.props.item?.quantity,
                description: this.$page.props.item?.description,
                scheduleType: this.$page.props.item?.schedule_type.toString(),
                scheduleParameter: this.$page.props.item?.schedule_parameter,
                limitType: this.$page.props.item?.limit_type,
            },
            errorMessage: {},
            id: this.$page.props.item?.id
        };
    },
    props: {
        bankAccounts: {
            type: Array,
            required: true
        },
        categories: {
            type: Array,
            required: true
        }
    },
    computed: {
        nameErrors() {
            const errors = [];
            if (!this.$v.fields.name.$dirty) return errors;
            !this.$v.fields.name.maxLength &&
                errors.push("Name must be at most 250 characters long");
            !this.$v.fields.name.required && errors.push("Name is required.");
            return errors;
        },
        bankAccountErrors() {
            const errors = [];
            if (!this.$v.fields.bankAccountId.$dirty) return errors;
            !this.$v.fields.bankAccountId.required &&
                errors.push("Bank account is required.");
            return errors;
        },
        categoryErrors() {
            const errors = [];
            if (!this.$v.fields.categoryId.$dirty) return errors;
            !this.$v.fields.categoryId.required &&
                errors.push("Category is required.");
            return errors;
        },
        dayRepeatErrors() {
            const errors = [];
            if (!this.$v.dayRepeat.$dirty) return errors;
            !this.$v.dayRepeat.required && errors.push("Field is required.");
            return errors;
        },
        disableSave() {
            return this.$v.$invalid || this.isSaving;
        },
        title() {
            return this.id
                ? "Update recurring transaction"
                : "Add a recurring transaction";
        }
    },
    methods: {
        goBack() {
            this.$inertia.get(route(routeName + ".index"));
        },
        getScheduleParameter() {
            switch (this.fields.scheduleType) {
                case "1":
                    return this.dayRepeat;
                case "2":
                    return this.dayOfTheMonth;
                case "3":
                    return "lastDayOfTheMonth";
                case "4":
                    return this.dayOfTheWeek;
            }
        },
        save(redirect) {
            // Clear errors
            this.$v.$reset();

            // Check for errors
            this.$v.$touch();
            this.isSaving = true;

            const urlAction = this.id ? ".update" : ".store";

            const storeData = this.fields;
            console.log(storeData);
            storeData.scheduleParameter = this.getScheduleParameter();

            if (this.fields.limitType === 'Number of transactions') {
                delete storeData.endDate;
            } else if(this.fields.limitType === 'Date') {
                delete storeData.quantity;
            } else {
                delete storeData.endDate;
                delete storeData.quantity;
            }
            console.log(storeData);

            axios({
                url: route(routeName + urlAction, this.id),
                method: this.id ? "PUT" : "POST",
                data: {
                    item: storeData
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
    validations() {
        return {
            fields: {
                name: { required, maxLength: maxLength(250) },
                bankAccountId: { required },
                categoryId: { required }
            },
            dayRepeat: {
                required: requiredIf(() => this.fields.scheduleType === "1"),
                minValue: minValue(1)
            }
        };
    }
};
</script>

<style scoped>
.daysInput {
}
</style>
