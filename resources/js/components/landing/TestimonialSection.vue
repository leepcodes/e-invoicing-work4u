<script setup lang="ts">
import { useAOS } from "@/composables/useAOS";

useAOS();

const testimonials = [
    {
        name: "Jane Doe",
        role: "CEO, Startup Inc.",
        star: "/images/star.webp",
        message: "This product completely transformed our workflow!",
    },
    {
        name: "John Smith",
        role: "Marketing Lead",
        star: "/images/star.webp",
        message: "Amazing support and fantastic results. Highly recommended.",
    },
    {
        name: "Emily Johnson",
        role: "Developer",
        star: "/images/star.webp",
        message: "Clean design, easy to use, and very effective.",
    },
];

const avatarColors = [
    "bg-blue-600",
    "bg-green-600",
    "bg-purple-600",
    "bg-orange-600",
    "bg-pink-600",
    "bg-indigo-600",
    "bg-teal-600",
    "bg-red-600",
];

const getInitials = (name: string) =>
    name
        .split(" ")
        .map((word) => word.charAt(0))
        .join("")
        .toUpperCase();

const getAvatarColor = (name: string) => {
    const index = [...name].reduce(
        (sum, char) => sum + char.charCodeAt(0),
        0,
    );

    return avatarColors[index % avatarColors.length];
};
</script>

<template>
    <section class="bg-gray-50 py-10">
        <h1
            class="pb-10 text-center text-4xl font-extrabold text-gray-900"
            data-aos="fade-up"
        >
            Testimonials
        </h1>

        <div
            class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-6 md:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="(t, index) in testimonials"
                :key="index"
                class="flex flex-col items-start rounded-xl bg-white p-6 shadow"
                data-aos="fade-up"
                :data-aos-delay="index * 100"
            >
                <div class="flex w-full items-center gap-3">
                    <div
                        :class="[
                            'flex h-16 w-16 shrink-0 items-center justify-center rounded-full text-xl font-bold text-white',
                            getAvatarColor(t.name),
                        ]"
                    >
                        {{ getInitials(t.name) }}
                    </div>

                    <div class="text-left">
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ t.name }}
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ t.role }}
                        </p>
                    </div>
                </div>

                <p class="mt-5 text-left leading-relaxed text-gray-700 italic">
                    "{{ t.message }}"
                </p>

                <div class="mt-4">
                    <img
                        :src="t.star"
                        alt="5 star rating"
                        class="h-auto w-28 object-contain"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
