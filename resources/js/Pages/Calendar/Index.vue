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
                    v-if="days"
                    @change="getData"
                >
                    <template v-slot:day-label="{ day, month, date, present }">
                        <span @click="showDay($event, date)">
                            <span :class="{ today: present }">
                                {{ day }}
                            </span>
                            <span class="day-label balance">{{
                                getBalance(month, day)
                            }}</span>
                            <span class="day-label available">{{
                                getAvailable(month, day)
                            }}</span>
                        </span>
                    </template>
                    <template v-slot:event="{ event }">
                        <span
                            @click="showDay($event, event.start)"
                            :class="{
                                'tw-text-green-600': event.color === 'green',
                                'tw-text-red-600': event.color === 'red',
                                'tw-text-blue-600': event.color === 'blue'
                            }"
                            class="tw-block tw-text-center tw-w-full tw-bg-white tw-text-right"
                            >{{ event.name }}
                            <template v-if="event.isPending">
                                *
                            </template>
                        </span>
                    </template>
                </v-calendar>
                <v-menu
                    v-model="selectedOpen"
                    :close-on-content-click="false"
                    :close-on-click="false"
                    :activator="selectedElement"
                    class="dialog"
                >
                    <v-card color="grey lighten-4" min-width="350px" flat>
                        <v-toolbar :color="selectedEvent.color" dark>
                            <v-toolbar-title
                                v-html="selectedDay.date"
                            ></v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn icon class="addButton" @click="add">
                                <v-icon>mdi-plus</v-icon>
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
                                @click="edit(transaction)"
                                >{{ transaction.description }}
                                <template v-if="transaction.status !== 1">
                                    *
                                </template>
                                <v-spacer />
                                ${{ transaction.amount }}
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
        <transaction-dialog
            :dialog="dialog"
            :categories="categories"
            :selectedItem="selectedItem"
            :defaultDate="selectedDay.date"
            @cancel="dialog = selectedOpen = false"
            @save="save"
            @delete="deleteTransaction"
        ></transaction-dialog>
    </v-row>
</template>

<script>
import DashboardLayout from "@/Layouts/Dashboard";
import TransactionDialog from "@/Components/Transaction";
import moment from "moment";

const formatter = new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 2
});
export default {
    layout: DashboardLayout,
    components: {
        TransactionDialog
    },
    props: {
        date: { type: String, required: true },
        categories: { type: Array, required: true },
        bankAccountId: { type: Number, required: true }
    },
    data() {
        return {
            currentDate: this.$page.props.date,
            selectedItem: {},
            selectedEvent: {},
            selectedElement: null,
            selectedOpen: false,
            selectedDay: {},
            loadData: false,
            title: "",
            isLoading: false,
            dialog: false,
            transactions: this.$page.props.transactions,
            events: this.$page.props.events,
            days: this.$page.props.days
        };
    },
    mounted() {
        this.$once(
            "hook:destroyed",
            this.$inertia.on("finish", event => {
                this.isLoading = false;

                // Refresh entire calendar data
                this.events = this.$page.props.events;
                this.days = this.$page.props.days;
                this.transactions = this.$page.props.transactions;

                const transactions = this.transactions.filter(
                    item => item.date === this.selectedDay.date
                );

                this.selectedDay.transactions = transactions;
            })
        );
    },

    computed: {},
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
        showDay(event, date) {
            this.selectedElement = event.target;
            const dateObject = moment(date + "T00:00:00");
            const transactions = this.transactions.filter(
                item => item.date === dateObject.format("YYYY-MM-DD")
            );

            const open = () => {
                this.selectedDay = {
                    date: dateObject.format("YYYY-MM-DD"),
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
            return this.days[month]
                ? formatter.format(this.days[month]?.[day]?.balance)
                : "";
        },
        getAvailable(month, day) {
            return this.days[month]
                ? formatter.format(this.days[month][day]?.available)
                : "";
        },
        add() {
            this.selectedItem = {
                date: this.selectedDay.date,
                bankAccountId: this.bankAccountId,
                categoryId: "",
                amount: "",
                description: "",
                isPending: false
            };
            this.dialog = true;
        },
        edit(transaction) {
            this.selectedItem = {
                id: transaction.id,
                date: transaction.date,
                categoryId: transaction.category_id,
                amount: transaction.amount,
                description: transaction.description,
                isPending: transaction.status === 2,
                bankAccountId: this.bankAccountId
            };
            this.dialog = true;
        },
        deleteTransaction(item) {
            // Make request to delete from database
            axios.delete(route("transactions.destroy", item.id)).then(() => {
                // this.transactions = this.transactions.filter(
                //     transaction => transaction.id !== item.id
                // );

                // // Update transactions
                // this.selectedDay.transactions = this.transactions.filter(
                //     transaction => transaction.date === item.date
                // );

                // // Update event
                // const event = this.events.find(
                //     event => event.start === item.date
                // );
                // if (event) {
                //     event.name = parseFloat(
                //         event.name - parseFloat(item.amount)
                //     );
                //     if (event.name === 0) {
                //         this.events = this.events.filter(
                //             event => event.start !== item.date
                //         );
                //     }
                // }

                // // Update balances
                // const dateObject = moment(item.date + "T00:00:00");
                // console.log(
                //     this.days[dateObject.month() + 1][dateObject.date()].balance
                // );
                // console.log(item.amount);
                // this.days[dateObject.month() + 1][
                //     dateObject.date()
                // ].balance += parseFloat(item.amount);
                // this.days[dateObject.month() + 1][
                //     dateObject.date()
                // ].available += parseFloat(item.amount);

                this.dialog = false;
                this.loadData = true;
                const date = moment(item.date);

                this.getData({
                    start: {
                        date: item.date,
                        month: date.month() + 1,
                        year: date.year()
                    }
                });
            });
        },
        save(item) {
            item.status = item.isPending ? 2 : 1;
            const urlAction = item.id ? ".update" : ".store";
            axios({
                url: route("transactions" + urlAction, item.id),
                method: item.id ? "PUT" : "POST",
                data: item
            })
                .then(response => {
                    //FIXME: Code needs to be fixed to refresh all the balances, events and transaction information

                    // if (item.id) {
                    // // Refresh events and transaction objects with the new item information
                    // const modifiedTransaction = this.transactions.find(
                    //     transaction => transaction.id === item.id
                    // );
                    // const oldAmount = modifiedTransaction.amount;
                    // modifiedTransaction.amount = item.amount;
                    // modifiedTransaction.category_id = item.categoryId;
                    // modifiedTransaction.date = item.date;
                    // modifiedTransaction.description = item.description;
                    // modifiedTransaction.status = item.status;

                    // const modifiedEvent = this.events.find(
                    //     event => event.start === item.date
                    // );
                    // modifiedEvent.name = (
                    //     parseFloat(modifiedEvent.name) -
                    //     parseFloat(oldAmount) +
                    //     parseFloat(item.amount)
                    // ).toFixed(2);
                    // } else {
                    //     item.id = response.data;

                    //     // Add new item to events and transactions
                    // }
                    this.dialog = false;
                    this.loadData = true;
                    const date = moment(item.date);

                    this.getData({
                        start: {
                            date: item.date,
                            month: date.month() + 1,
                            year: date.year()
                        }
                    });
                })
                .catch(error => {
                    this.errorMessage = error.response?.data.errors || [];
                })
                .then(() => {});
        }
    }
};
</script>

<style lang="postcss">
.v-event {
    margin-left: 1px;
}

.col {
    @apply tw-px-2;
}
</style>

<style lang="postcss" scoped>
.today {
    @apply tw-bg-blue-200 tw-rounded-full tw-p-1;
}

.day-label {
    @apply tw-block tw-text-xs tw-text-blue-500 tw-text-right tw-truncate;
    font-size: 0.65rem;
    margin-right: 2px;
}

.available {
    @apply tw-text-green-500 tw-mb-2;
}

.addButton {
    margin-right: 0.25rem !important;
}
</style>
