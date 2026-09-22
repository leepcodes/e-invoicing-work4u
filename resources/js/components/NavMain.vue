<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';

import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();

const openItems = ref<Record<string, boolean>>({});

const toggleItem = (title: string) => {
    openItems.value[title] = !openItems.value[title];
};

const isOpen = (title: string) => {
    return openItems.value[title] ?? false;
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="text-black">
            Platform
        </SidebarGroupLabel>

        <SidebarMenu>
            <SidebarMenuItem
                v-for="item in items"
                :key="item.title"
            >

                <!-- Normal navigation item -->
                <SidebarMenuButton
                    v-if="!item.children?.length"
                    class="hover:bg-slate-100 hover:text-blue-500"
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>

                <!-- Parent navigation item -->
                <SidebarMenuButton
                    v-else
                    class="hover:bg-slate-100 hover:text-blue-500"
                    :is-active="
                        item.children.some(child =>
                            isCurrentUrl(child.href)
                        )
                    "
                    :tooltip="item.title"
                    @click="toggleItem(item.title)"
                >
                    <component :is="item.icon" />

                    <span>{{ item.title }}</span>

                    <!-- Arrow -->
                    <ChevronRight
                        class="ml-auto transition-transform duration-200"
                        :class="{
                            'rotate-90': isOpen(item.title)
                        }"
                    />
                </SidebarMenuButton>

                <!-- Child navigation items -->
                <SidebarMenu
                    v-if="item.children?.length && isOpen(item.title)"
                    class="ml-4"
                >
                    <SidebarMenuItem
                        v-for="child in item.children"
                        :key="child.title"
                    >
                        <SidebarMenuButton
                            class="hover:bg-slate-100 hover:text-blue-500"
                            as-child
                            :is-active="isCurrentUrl(child.href)"
                            :tooltip="child.title"
                        >
                            <Link :href="child.href">
                                <component :is="child.icon" />
                                <span>{{ child.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>

            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
