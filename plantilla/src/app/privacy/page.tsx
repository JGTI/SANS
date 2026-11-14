"use client";

import Link from "next/link";
import Privacy from "./privacy.mdx";

type PrivacyLinkProps = React.AnchorHTMLAttributes<HTMLAnchorElement> & {
  href?: string;
};

const PrivacyLink = ({ href = "", children, ...props }: PrivacyLinkProps) => {
  if (href.startsWith("/")) {
    return (
      <Link href={href} {...props}>
        {children}
      </Link>
    );
  }

  return (
    <a href={href} {...props}>
      {children}
    </a>
  );
};

const Page = () => {
  return (
    <section className="mx-auto max-w-2xl px-4 py-28 lg:pt-44 lg:pb-32">
      <article className="prose prose-lg dark:prose-invert">
        <Privacy components={{ a: PrivacyLink }} />
      </article>
    </section>
  );
};

export default Page;
