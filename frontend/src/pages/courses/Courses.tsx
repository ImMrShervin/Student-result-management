import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { DataTable } from '@/components/ui/DataTable';

export default function Courses() {
  const { data, isLoading } = useQuery({
    queryKey: ['courses'],
    queryFn: async () => (await api.get('/courses', { params: { per_page: 30 } })).data,
  });
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Courses</h1>
      <DataTable
        loading={isLoading}
        rows={data?.data ?? []}
        columns={[
          { key: 'code', header: 'Code', render: (r: any) => <span className="font-mono">{r.code}</span> },
          { key: 'title', header: 'Title' },
          { key: 'department', header: 'Department', render: (r: any) => r.department?.name },
          { key: 'theory', header: 'Theory', render: (r: any) => r.theory_credit },
          { key: 'practical', header: 'Practical', render: (r: any) => r.practical_credit },
          { key: 'total', header: 'Total', render: (r: any) => <b>{r.total_credit}</b> },
        ]}
      />
    </div>
  );
}
