import { onMounted } from "vue";
import { router } from "@inertiajs/vue3";

export function useAOS() {
    onMounted(async () => {

        const AOS = await import("aos");
        await import("aos/dist/aos.css");

        AOS.default.init({
            duration: 800,
            once: true,
            offset: 100,
        });

        router.on("finish", () => {
            AOS.default.refresh();
        });
    });
}
