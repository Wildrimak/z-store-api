<?php
class Usuario_Model extends CI_Model
{
    public function fetch_all()
    {
        $this->db->order_by('id', 'ASC');
        return $this->db->get('usuarios');
    }

    public function get($user_id)
    {
        $this->db->where('id', $user_id);
        $query = $this->db->get('usuarios');
        return $query->result_array();
    }

    public function get_by_matricula($matricula)
    {
        $this->db->where('matricula', $matricula);
        $query = $this->db->get('usuarios');
        return $query->result_array();
    }


    public function insert($data)
    {
        $this->db->insert('usuarios', $data);
    }

    public function update($user_id, $data)
    {
        $this->db->where('id', $user_id);
        $this->db->update('usuarios', $data);
    }

    public function delete($user_id)
    {
        $this->db->where('id', $user_id);
        $this->db->delete('usuarios');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
