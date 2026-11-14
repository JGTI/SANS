import { FaWhatsapp } from "react-icons/fa";

export function FloatingWhatsApp() {
  return (
    <a
      href="https://wa.me/524461160221"
      target="_blank"
      rel="noreferrer"
      aria-label="Escríbenos por WhatsApp"
      className="bg-foreground text-background fixed right-5 bottom-5 z-50 flex size-14 items-center justify-center rounded-full border border-border shadow-xl transition-transform duration-200 hover:scale-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-foreground"
    >
      <FaWhatsapp className="size-8" aria-hidden="true" />
    </a>
  );
}
