<template>
    <v-row class="fill-height">
        <v-progress-linear indeterminate v-if="isLoading"></v-progress-linear>
        <v-col>
            <v-sheet height="64">
                <v-toolbar flat>
                    <v-btn
                        outlined
                        class="mr-4"
                        color="grey darken-2"
                        @click="setToday"
                    >
                        Today
                    </v-btn>
                    <v-btn fab text small color="grey darken-2" @click="prev">
                        <v-icon small>
                            mdi-chevron-left
                        </v-icon>
                    </v-btn>
                    <v-btn fab text small color="grey darken-2" @click="next">
                        <v-icon small>
                            mdi-chevron-right
                        </v-icon>
                    </v-btn>
                    <v-toolbar-title>
                        {{ title }}
                    </v-toolbar-title>
                </v-toolbar>
            </v-sheet>
            <v-sheet height="750">
                <v-calendar
                    v-model="currentDate"
                    ref="calendar"
                    color="primary"
                    :events="events"
                    :event-color="getEventColor"
                    type="month"
                    @change="getData"
                >
                    <template v-slot:day-label="{ day, month, date, present }">
                        <span
                            :class="{ today: present }"
                            @click="showDay(date)"
                        >
                            {{ day }}
                        </span>
                        <span class="day-label balance">{{
                            getBalance(month, day)
                        }}</span>
                        <span class="day-label available">{{
                            getAvailable(month, day)
                        }}</span>
                    </template>
                    <template v-slot:event="{ event }">
                        <span class="tw-block tw-text-center tw-w-full">{{
                            event.name
                        }}</span>
                    </template>
                </v-calendar>
                <v-menu
                    v-model="selectedOpen"
                    :close-on-content-click="false"
                    :activator="selectedElement"
                    absolute
                >
                    <v-card color="grey lighten-4" min-width="350px" flat>
                        <v-toolbar :color="selectedEvent.color" dark>
                            <v-toolbar-title
                                v-html="selectedDay.date"
                            ></v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn icon>
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </v-toolbar>
                        <v-card-text>
                            <v-btn
                                block
                                small
                                dark
                                :color="transaction.color"
                                v-for="(transaction,
                                index) in selectedDay.transactions"
                                :key="index"
                                class="tw-mt-2"
                                >{{ transaction.description }}
                                <v-spacer />
                                {{ transaction.amount }}
                            </v-btn>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn
                                text
                                color="secondary"
                                @click="selectedOpen = false"
                            >
                                Close
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-menu>
            </v-sheet>
        </v-col>
    </v-row>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import moment from "moment";

export default {
    layout: DashboardLayout,
    props: {
        events: { type: Array, required: true },
        days: { type: Object, required: true },
        transactions: { type: Array, required: true },
        date: { type: String, required: true }
    },
    data() {
        return {
            currentDate: this.$page.props.date,
            selectedEvent: {},
            selectedElement: null,
            selectedOpen: false,
            selectedDay: {},
            loadData: false,
            title: "",
            isLoading: false
        };
    },
    mounted() {
        this.$once(
            "hook:destroyed",
            this.$inertia.on("finish", event => {
                this.isLoading = false;
            })
        );
    },
    methods: {
        getEventColor(event) {
            return event.color;
        },
        setToday() {
            this.focus = "";
        },
        prev() {
            this.loadData = true;
            this.$refs.calendar.prev();
        },
        next() {
            this.loadData = true;
            this.$refs.calendar.next();
        },
        getData({ start }) {
            this.title = moment(start.date).format("MMMM Y");
            if (this.loadData) {
                this.isLoading = true;
                this.$inertia.get(
                    route("calendar.index", {
                        year: start.year,
                        month: start.month
                    }),
                    {},
                    { preserveState: true }
                );

                this.loadData = false;
            }
        },
        showDay(date) {
            const dateObject = moment(date + "T00:00:00");
            const transactions = this.transactions.filter(
                item => item.date === dateObject.format("YYYY-MM-DD")
            );

            const open = () => {
                this.selectedDay = {
                    date: dateObject.format("MM/DD/YYYY"),
                    transactions
                };
                requestAnimationFrame(() =>
                    requestAnimationFrame(() => (this.selectedOpen = true))
                );
            };

            if (this.selectedOpen) {
                this.selectedOpen = false;
                requestAnimationFrame(() =>
                    requestAnimationFrame(() => open())
                );
            } else {
                open();
            }
        },
        getBalance(month, day) {
            return this.days[month] ? this.days[month][day]?.balance : "";
        },
        getAvailable(month, day) {
            return this.days[month] ? this.days[month][day]?.available : "";
        }
    }
};
</script>

<style lang="postcss" scoped>
.today {
    @apply tw-bg-blue-200 tw-rounded-full tw-p-1;
}

.day-label {
    @apply tw-block tw-text-xs tw-text-blue-500 tw-text-right tw-truncate;
}

.available {
    @apply tw-text-green-500 tw-mb-2;
}
</style>
