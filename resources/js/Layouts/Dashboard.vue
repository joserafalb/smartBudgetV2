<template>
    <v-app>
        <v-navigation-drawer app v-model="drawer">
            <v-list>
                <v-list-group :value="false" no-action>
                    <template v-slot:activator>
                        <v-list-item-content>
                            <v-list-item-title
                                >Manage Accounts</v-list-item-title
                            >
                        </v-list-item-content>
                    </template>
                    <v-list-item link>
                        <v-list-item-title @click="$inertia.visit('/banks')"
                            >Banks</v-list-item-title
                        >
                    </v-list-item>
                    <v-list-item link>
                        <v-list-item-title>Account Type</v-list-item-title>
                    </v-list-item>
                    <v-list-item link>
                        <v-list-item-title>Bank Accounts</v-list-item-title>
                    </v-list-item>
                </v-list-group>
                <v-list-group :value="false" no-action>
                    <template v-slot:activator>
                        <v-list-item-content>
                            <v-list-item-title>Transactions</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item link>
                        <v-list-item-title>Categories</v-list-item-title>
                    </v-list-item>
                    <v-list-item link>
                        <v-list-item-title
                            >Recurring Transactions</v-list-item-title
                        >
                    </v-list-item>
                </v-list-group>
                <v-list-item link>
                    <v-list-item-title>Calendar</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>Smart Budget</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu bottom min-width="200px" rounded offset-y>
                <template v-slot:activator="{ on }">
                    <v-btn icon x-large v-on="on">
                        <v-avatar color="primary" size="36">
                            <span class="white--text">{{
                                user.initials
                            }}</span>
                        </v-avatar>
                    </v-btn>
                </template>
                <v-card>
                    <v-list-item-content class="justify-center">
                        <div class="mx-auto text-center">
                            <v-avatar color="primary" size="36">
                                <span class="white--text">{{
                                    user.initials
                                }}</span>
                            </v-avatar>
                            <h3>{{ user.fullName }}</h3>
                            <p class="text-caption mt-1">
                                {{ user.email }}
                            </p>
                            <v-divider class="my-3"></v-divider>
                            <v-btn text>
                                Edit Profile
                            </v-btn>
                            <v-divider class="my-3"></v-divider>
                            <v-btn text @click="logout">
                                Logout
                            </v-btn>
                        </div>
                    </v-list-item-content>
                </v-card>
            </v-menu>
        </v-app-bar>

        <!-- Sizes your content based upon application components -->
        <v-main>
            <!-- Provides the application the proper gutter -->
            <v-container fluid>
                <slot></slot>
            </v-container>
        </v-main>

        <v-footer app>
            <!-- -->
        </v-footer>
    </v-app>
</template>

<script>
export default {
    data: () => ({
        drawer: false,
        user: {
            initials: "JD",
            fullName: "John Doe",
            email: "john.doe@doe.com"
        }
    }),
    methods: {
        logout() {
            this.$inertia.post("/logout");
        }
    }
};
</script>
