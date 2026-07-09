import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { DataTable } from '@/components/ui/DataTable';

export default function Enrollments() {
  const { data, isLoading } = useQuery({
    queryKey: ['enrollments'],
    queryFn: async () => (await api.get('/enrollments', { params: { per_page: 30 } })).data,
  });
  const statusColor: Record<string, string> = {
    approved: 'bg-emerald-100 text-emerald-800',
    pending: 'bg-yellow-100 text-yellow-800',
    rejected: 'bg-red-100 text-red-800',
    withdrawn: 'bg-slate-100 text-slate-800',
    completed: 'bg-blue-100 text-blue-800',
  };
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Enrollments</h1>
      <DataTable
        loading={isLoading}
        rows={data?.data ?? []}
        columns={[
          { key: 'student', header: 'Student', render: (r: any) => r.student?.user?.first_name + ' ' + r.student?.user?.last_name },
          { key: 'course', header: 'Course', render: (r: any) => `${r.course_section?.course?.code} — ${r.course_section?.course?.title}` },
          { key: 'section', header: 'Section', render: (r: any) => r.course_section?.section_code },
          { key: 'status', header: 'Status', render: (r: any) => <span className={`badge ${statusColor[r.status] ?? 'bg-slate-100'}`}>{r.status}</span> },
          { key: 'enrolled_at', header: 'Enrolled', render: (r: any) => new Date(r.enrolled_at).toLocaleDateString() },
        ]}
      />
    </div>
  );
}
