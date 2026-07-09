import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { DataTable } from '@/components/ui/DataTable';

export default function Grades() {
  const { data, isLoading } = useQuery({
    queryKey: ['grades'],
    queryFn: async () => (await api.get('/grades', { params: { per_page: 30 } })).data,
  });
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Grades</h1>
      <DataTable
        loading={isLoading}
        rows={data?.data ?? []}
        columns={[
          { key: 'id', header: 'ID' },
          { key: 'total_score', header: 'Total', render: (r: any) => Number(r.total_score).toFixed(2) },
          { key: 'letter_grade', header: 'Letter', render: (r: any) => <span className="badge bg-brand-100 text-brand-800">{r.letter_grade}</span> },
          { key: 'gpa', header: 'GPA', render: (r: any) => Number(r.gpa_points).toFixed(2) },
          { key: 'published', header: 'Published', render: (r: any) => r.is_published ? '✓' : '—' },
        ]}
      />
    </div>
  );
}
