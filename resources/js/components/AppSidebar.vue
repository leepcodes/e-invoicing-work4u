<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    FileText,
    LayoutGrid,
    UserRound,
    Users,
    Mail,
    Building2,
    Package,
} from '@lucide/vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const footerNavItems: NavItem[] = [];

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard', href: dashboard(), icon: LayoutGrid,
    },
    {
        title: 'Invoice', href: '#', icon: FileText,
        children: [ { title: 'Invoice', href: '/invoice', icon: FileText, },],
    },
    {
        title: 'Item', href: '#', icon: Package,
        children: [ { title: 'Item', href: '/item', icon: Package,},],
    },
    {
        title: 'Customer', href: '#', icon: Users,
        children: [ { title: 'Buyer List', href: '/buyer', icon: UserRound,},],
    },
    {
        title: 'Mailer', href: '#', icon: Mail,
        children: [ { title: 'Email List', href: '/mailer', icon: Mail,},],
    },
    {
        title: 'Seller', href: '#', icon: Building2,
        children: [ { title: 'Seller Profile', href: '/seller', icon: UserRound,},],
    },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="overflow-x-hidden bg-white text-black"
    >
        <SidebarHeader class="w-full overflow-x-hidden bg-white text-black">
            <SidebarMenu class="overflow-x-hidden">
                <SidebarMenuItem class="overflow-x-hidden">
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="font-bold text-black"
                    >
                        <Link href="#" class="min-w-0">
                            <img
                                src="/images/logo/example.logo.no-bg.webp"
                                alt="E-invoicing"
                                class="h-10 w-10 shrink-0"
                            />
                            <span class="truncate">E-invoicing</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent
            class="overflow-x-hidden bg-white text-black"
        >
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter
            class="overflow-x-hidden bg-white text-black"
        >
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
