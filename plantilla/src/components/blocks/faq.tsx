import Link from "next/link";
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from "@/components/ui/accordion";
import { cn } from "@/lib/utils";

const categories = [
  { title: "Software empresarial", questions: [
    { question: "¿Cuándo conviene desarrollar software a la medida?", answer: "Cuando un proceso crítico depende de archivos, sistemas aislados o herramientas genéricas que ya no responden a la operación." },
    { question: "¿Pueden mejorar un sistema que ya tenemos?", answer: "Sí. Analizamos la aplicación, sus datos y procesos para modernizarla sin perder de vista la continuidad operativa." },
    { question: "¿Trabajan con empresas que dependen de Excel?", answer: "Sí. Podemos convertir procesos basados en archivos en sistemas centralizados que crecen por módulos." },
  ] },
  { title: "Integraciones y datos", questions: [
    { question: "¿Pueden conectar nuestros sistemas actuales?", answer: "Integramos ERP, CRM, facturación, proveedores, correo, GPS y APIs para evitar captura duplicada." },
    { question: "¿Cómo inician un proyecto?", answer: "Primero entendemos el proceso, identificamos el problema y definimos una solución con alcance claro." },
  ] },
  { title: "PyMEs", questions: [
    { question: "¿Necesito implementar todo de una vez?", answer: "No. Podemos comenzar por el proceso más crítico y hacer crecer la plataforma conforme crece tu empresa." },
    { question: "¿Ofrecen precios fijos?", answer: "Cada proyecto se cotiza según su alcance, integraciones, datos y necesidades operativas." },
  ] },
];

export const FAQ = ({ headerTag = "h2", className, className2 }: { headerTag?: "h1" | "h2"; className?: string; className2?: string }) => (
  <section className={cn("py-28 lg:py-32", className)}>
    <div className="container max-w-5xl"><div className={cn("mx-auto grid gap-16 lg:grid-cols-2", className2)}>
      <div className="space-y-4">{headerTag === "h1" ? <h1 className="text-2xl tracking-tight md:text-4xl lg:text-5xl">Preguntas frecuentes</h1> : <h2 className="text-2xl tracking-tight md:text-4xl lg:text-5xl">Preguntas frecuentes</h2>}<p className="text-muted-foreground max-w-md leading-snug lg:mx-auto">Si quieres revisar un proceso específico, <Link href="/contact" className="underline underline-offset-4">cuéntanos tu proyecto</Link>.</p></div>
      <div className="grid gap-6 text-start">{categories.map((category, categoryIndex) => <div key={category.title}><h3 className="text-muted-foreground border-b py-4">{category.title}</h3><Accordion type="single" collapsible className="w-full">{category.questions.map((item, index) => <AccordionItem key={item.question} value={`${categoryIndex}-${index}`}><AccordionTrigger>{item.question}</AccordionTrigger><AccordionContent className="text-muted-foreground">{item.answer}</AccordionContent></AccordionItem>)}</Accordion></div>)}</div>
    </div></div>
  </section>
);
