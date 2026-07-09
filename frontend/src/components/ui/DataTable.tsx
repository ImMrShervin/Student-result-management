import { ReactNode } from 'react';

export type Column<T> = { key: string; header: string; render?: (row: T) => ReactNode; className?: string };

export function DataTable<T extends { id: number | string }>({
  columns, rows, loading, empty = 'No data',
}: { columns: Column<T>[]; rows: T[]; loading?: boolean; empty?: string }) {
  if (loading) return <div className="skeleton h-64" />;
  if (!rows?.length) return <div className="text-center py-12 text-slate-500">{empty}</div>;
  return (
    <div className="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
      <table className="table">
        <thead>
          <tr>{columns.map((c) => <th key={c.key} className={c.className}>{c.header}</th>)}</tr>
        </thead>
        <tbody className="divide-y divide-slate-200 dark:divide-slate-700">
          {rows.map((r) => (
            <tr key={r.id}>
              {columns.map((c) => (
                <td key={c.key} className={c.className}>
                  {c.render ? c.render(r) : (r as any)[c.key]}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
