import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { DataTable } from '@/components/ui/DataTable';

export default function Teachers() {
  const { data, isLoading } = useQuery({
    queryKey: ['teachers'],
    queryFn: async () => (await api.get('/teachers', { params: { per_page: 20 } })).data,
  });
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Teachers</h1>
      <DataTable
        loading={isLoading}
        rows={data?.data ?? []}
        columns={[
          { key: 'employee_code', header: 'Employee #' },
          { key: 'name', header: 'Name', render: (r: any) => `${r.user?.first_name} ${r.user?.last_name}` },
          { key: 'department', header: 'Department', render: (r: any) => r.department?.name },
          { key: 'rank', header: 'Rank', render: (r: any) => r.academic_rank },
          { key: 'email', header: 'Email', render: (r: any) => r.user?.email },
        ]}
      />
    </div>
  );
}
