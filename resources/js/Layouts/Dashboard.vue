<template>
    <v-app>
        <v-navigation-drawer app v-model="drawer" v-if="$vuetify.breakpoint.sm">
            <v-list>
                <div v-for="(menu, index) in menuItems" :key="index">
                    <v-list-group v-if="menu.items" :value="false" no-action>
                        <template v-slot:activator>
                            <v-list-item-content>
                                <v-list-item-title>{{
                                    menu.title
                                }}</v-list-item-title>
                            </v-list-item-content>
                        </template>
                        <v-list-item
                            v-for="(subMenu, indexSubMenu) in menu.items"
                            :key="indexSubMenu"
                            link
                        >
                            <v-list-item-title
                                @click="$inertia.visit(subMenu.inertia)"
                                >{{ subMenu.title }}</v-list-item-title
                            >
                        </v-list-item>
                    </v-list-group>
                    <v-list-item v-else link>
                        <v-list-item-title @click="$inertia.visit(menu.inertia)"
                            >{{ menu.title }}
                        </v-list-item-title>
                    </v-list-item>
                </div>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app>
            <v-app-bar-nav-icon
                v-if="$vuetify.breakpoint.sm"
                @click="drawer = !drawer"
            ></v-app-bar-nav-icon>
            <v-toolbar-title>Smart Budget</v-toolbar-title>
            <div v-for="(menu, index) in menuItems">
                <v-menu open-on-hover offset-y :key="index" v-if="menu.items">
                    <template v-slot:activator="{ on, attrs }">
                        <span v-bind="attrs" v-on="on" class="menuItem">
                            {{ menu.title }}
                        </span>
                    </template>
                    <v-list>
                        <v-list-item
                            v-for="(subMenu, indexSubMenu) in menu.items"
                            :key="indexSubMenu"
                            link
                        >
                            <v-list-item-title
                                @click="$inertia.visit(subMenu.inertia)"
                                >{{ subMenu.title }}</v-list-item-title
                            >
                        </v-list-item>
                    </v-list>
                </v-menu>
                <inertia-link v-else :href="menu.inertia" class="menuItem">
                    {{ menu.title }}
                </inertia-link>
            </div>
            <v-spacer></v-spacer>
            <v-menu bottom min-width="200px" rounded offset-y>
                <template v-slot:activator="{ on }">
                    <v-btn icon x-large v-on="on">
                        <v-avatar color="primary" size="36">
                            <span class="white--text">{{ user.initials }}</span>
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
import { Link } from "@inertiajs/inertia-vue";

export default {
    components: {
        Link: Link
    },
    data: () => ({
        drawer: false,
        user: {
            initials: "JD",
            fullName: "John Doe",
            email: "john.doe@doe.com"
        },
        menuItems: [
            {
                title: "Manage Accounts",
                items: [
                    { title: "Banks", inertia: "/banks" },
                    { title: "Account Type", inertia: "/account-type" },
                    { title: "Bank Accounts", inertia: "/bank-accounts" }
                ]
            },
            {
                title: "Transactions",
                items: [
                    { title: "Category Type", inertia: "/category-type" },
                    { title: "Categories", inertia: "/category" },
                    { title: "Goals", inertia: "/goal" },
                    {
                        title: "Recurring Transactions",
                        inertia: "/recurring-transaction"
                    }
                ]
            },
            { title: "Calendar", inertia: "/calendar" }
        ]
    }),
    methods: {
        logout() {
            this.$inertia.post("/logout");
        }
    }
};
</script>

<style scoped>
.menuItem {
    text-decoration: none;
}
</style>
